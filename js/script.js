$(document).ready(function() {
    
    var previewW = 0;
    var previewH = 0;
    var cropReady = false;

    // Dynamic loading image
    $('[name="photoTeacher"]').change( function(e) {
        var file = e.target.files[0];
        if (!file)
            return;

        var reader = new FileReader();
        reader.onload = function(e) {
            var content = e.target.result;
            content = btoa(content);

            var image = document.createElement('img');
            image.setAttribute('width', '100%');
            image.src = 'data:image/png;base64,'+ content;
            $('#js-crop-container').html('').append(image);

            var bgImage = $("[name='background']:checked + img");
            initPreview(bgImage, image);

            $('#js-crop-container img').imgAreaSelect({
                handles: true,
                show: true,
                aspectRatio: '3:4',

                onSelectChange: function preview(img, selection) {
                    var imgW = $(img).width()
                    var imgH = $(img).height();

                    var scaleX = previewW / (selection.width || 1);
                    var scaleY = previewH / (selection.height || 1);

                    $('#js-crop-preview img').css({
                        width: Math.round(scaleX * imgW) + 'px',
                        height: Math.round(scaleY * imgH) + 'px',
                        marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
                        marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
                    });
                },

                onSelectEnd: function (img, selection) {
                    $('input[name="x1"]').val(selection.x1);
                    $('input[name="y1"]').val(selection.y1);
                    $('input[name="x2"]').val(selection.x2);
                    $('input[name="y2"]').val(selection.y2);            
                }
            });

            cropReady = true;
        }
        reader.readAsBinaryString(file);
    });


    // Processing change background
    $("[name='background']").change(function(e) {
        if (cropReady) {
            var bgImage = $("[name='background']:checked + img");
            var img = $('#js-crop-container img').get(0);
            initPreview(bgImage, img);
        }
        return true;
    });

    initPreview = function(bgImage, image)
    {
        previewW = bgImage.data('w');
        previewH = bgImage.data('h');

        var style = $('#js-crop-preview img') ? $('#js-crop-preview img').attr('style') : '';

        $('#js-crop-preview').css({
            'left': bgImage.data('x'),
            'top': bgImage.data('y'),
            'width': previewW,
            'height': previewH,
        });

        var previewImage = $(image).clone();
        previewImage.attr({'style':style});
        previewImage.attr({
            'width': previewW+'px',
            'height': previewH+'px'
        });
        $('#js-crop-preview').html('').append(previewImage);

        bgImage = bgImage.clone();
        bgImage.removeAttr('width');
        $('#js-bg-preview').html('').append(bgImage.clone());
    }
});