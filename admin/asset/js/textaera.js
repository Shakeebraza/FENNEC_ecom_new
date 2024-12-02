// tinymce.init({
//     selector: 'textarea#default',
//     height: 300,
//     plugins: [
//         'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 
//         'preview', 'anchor', 'pagebreak', 'searchreplace', 'wordcount', 
//         'visualblocks', 'code', 'fullscreen', 'insertdatetime', 
//         'media', 'table', 'emoticons', 'template', 'codesample'
//     ],
//     toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullscreen | forecolor backcolor emoticons',
//     menu: {
//         favs: { title: 'Favorites', items: 'code visualaid | searchreplace | emoticons' }
//     },
//     menubar: 'favs file edit view insert format tools table',
//     content_style: 'body { font-family: Helvetica, Arial, sans-serif; font-size: 16px; }',
//     setup: function (editor) {
//         editor.on('change', function () {
//             editor.save(); // Automatically save content on change
//         });
//     }
// });

tinymce.init({
    selector: '#default',
    plugins: 'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    toolbar_mode: 'floating',
    height: 300,
    extended_valid_elements: 'a[href|target],img[src|alt|width|height],h1,h2,h3,h4,h5,h6,p,b,strong,i,em', // Allowed HTML tags
    valid_elements: '*[*]', // Allow all elements
    valid_styles: {
        '*': 'color,font-size,text-align' // Allow specific inline styles
    },
    entity_encoding: 'raw' // Ensures HTML entities are preserved
});