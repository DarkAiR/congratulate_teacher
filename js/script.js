$(document).ready(function() {
    
    var previewW = $('#js-crop-preview').width();
    var previewH = $('#js-crop-preview').height();

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

            var previewImage = document.createElement('img');
            previewImage.setAttribute('width', previewW+'px');
            previewImage.setAttribute('height', previewH+'px');
            previewImage.src = 'data:image/png;base64,'+ content;
            $('#js-crop-preview').html('').append(previewImage);

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
        }
        reader.readAsBinaryString(file);
    });
});