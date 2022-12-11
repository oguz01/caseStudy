@extends('dist.app')

@section('content')
    <div class="container pb-5">
        <div class="d-grid gap-5" style="grid-template-columns: 2fr;">
            <div class="bg-light border rounded-3">
                <form id="form" class="p-3" method="post" enctype="multipart/form-data">
                    <label for="files">Upload new Picture</label>
                    <input name="path" id="files" type="file" />
                    <input type="hidden" name="sort" value="{{ substr(time(), 7, 10) }}">
                </form>
                <div id="sortableImgThumbnailPreview" class="ui-sortable">

                </div>
            </div>
            <div id="sonuc" class="alert alert-success d-none"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        var $apiBase = '{{ env('APP_URL') }}/api/';
        $(function() {
            //Resimleri Api Üzerinden Listeleme
            $.ajax({
                url: $apiBase + 'image/',
                type: 'GET',
                success: function(data) {

                    $.each(data, function(i, v) {

                        $('#sortableImgThumbnailPreview').append(
                            '<div class="RearangeBox imgThumbContainer"><i class="material-icons imgRemoveBtn" onclick="removeThumbnailIMG(this,' +
                            v.id +
                            ')"><small class="btn btn-sm btn-danger">x</small></i><div class="IMGthumbnail"><img src="' +
                            v.path + '" data-key="' + v.id +
                            '"></div><div class="imgName"><small>' + v.path.replace(
                                "images/", "") + '</small></div>');
                    });


                }
            });

            //Apiye Resimi Kayıt Etme
            $('#files').on('change', function(e) {
                e.preventDefault();
                $.ajax({
                    url: $apiBase + 'image/',
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data: new FormData($('#form')[0]),
                    success: function(data) {
                        $('#sonuc').html(data).removeClass('d-none');
                        setTimeout(() => {
                            window.location.reload()
                        }, 100);
                    }
                });
            });

            //Resimleri Sıralama
            $("#sortableImgThumbnailPreview").sortable({
                connectWith: ".RearangeBox",
                start: function(event, ui) {
                    $(ui.item).addClass("dragElemThumbnail");
                    ui.placeholder.height(ui.item.height());
                },
                stop: function(event, ui) {
                    $(ui.item).removeClass("dragElemThumbnail");

                    //Sıralmayı db de Güncelleme
                    let update = [];
                    $('.RearangeBox').find('img').map(function(i) {
                        return update.push({
                            'key': $(this).data('key'),
                            'sort': i + 1
                        });
                    }).get();

                    $.ajax({
                        url: $apiBase + 'sort/',
                        type: 'PATCH',
                        data: {
                            data: update
                        },
                        success: function(data) {
                            $('#sonuc').html(data).removeClass('d-none');
                            setTimeout(() => {
                                $('#sonuc').addClass('d-none');
                            }, 2000);
                        }
                    });

                }
            });
            $("#sortableImgThumbnailPreview").disableSelection();
        });
        document.getElementById("files").addEventListener("change", handleFileSelect, false);

        // inputtan eklenen resimi listeye ekleme
        function handleFileSelect(evt) {
            var files = evt.target.files;
            var output = document.getElementById("sortableImgThumbnailPreview");
            for (var i = 0, f;
                (f = files[i]); i++) {

                if (!f.type.match("image.*")) {
                    continue;
                }

                var reader = new FileReader();
                reader.onload = (function(theFile) {
                    return function(e) {
                        var imgThumbnailElem =
                            "<div class='RearangeBox imgThumbContainer'><i class='material-icons imgRemoveBtn' onclick='removeThumbnailIMG(this)'><small class='btn btn-sm btn-danger'>x</small></i><div class='IMGthumbnail' ><img  src='" +
                            e.target.result +
                            "'" +
                            "title='" +
                            theFile.name +
                            "'/></div><div class='imgName'>" +
                            theFile.name +
                            "</div></div>";

                        output.innerHTML = output.innerHTML + imgThumbnailElem;
                    };
                })(f);
                reader.readAsDataURL(f);
            }
        }

        //Resimi silme işlemi
        function removeThumbnailIMG(elm, id = null) {
            elm.parentNode.outerHTML = "";
            $.ajax({
                url: $apiBase + 'image/' + id,
                type: 'DELETE',
                success: function(data) {
                    $('#sonuc').html(data).removeClass('d-none');
                    setTimeout(() => {
                        $('#sonuc').addClass('d-none');
                    }, 2000);
                }
            });
        }
    </script>
@endsection
