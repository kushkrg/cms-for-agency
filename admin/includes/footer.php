        </div>
    </main>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="no-referrer"></script>
    <script>
        tinymce.init({
            selector: '.tinymce-editor', // Target specific class
            height: 500,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:-apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size:14px }',
            promotion: false,
            // Enable title field in link dialog
            link_title: true,
            // Enable target list in link dialog
            link_target_list: [
                { title: 'None', value: '' },
                { title: 'New window', value: '_blank' }
            ]
        });
    </script>
    <script src="<?= e(ADMIN_URL) ?>/assets/js/admin.js"></script>
</body>
</html>
