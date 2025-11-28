<?php
// Central admin form definitions. Each form is an array of field definitions used by
// `admin/partials/admin_form.php` to render consistent input UIs across admin pages.

return [
    'team' => [
        ['type' => 'text', 'name' => 'name', 'label' => 'Name', 'required' => true],
        ['type' => 'text', 'name' => 'role', 'label' => 'Role'],
        ['type' => 'textarea', 'name' => 'bio', 'label' => 'Bio', 'rows' => 4],
        ['type' => 'file', 'name' => 'image', 'label' => 'Image', 'note' => 'Upload an image or choose existing below.'],
        ['type' => 'select', 'name' => 'existing_image', 'label' => 'Existing image', 'options' => []],
    ],

    'feedback' => [
        ['type' => 'text', 'name' => 'name', 'label' => 'Name', 'required' => true],
        ['type' => 'email', 'name' => 'email', 'label' => 'Email (optional)'],
        ['type' => 'text', 'name' => 'company', 'label' => 'Company (optional)'],
        ['type' => 'textarea', 'name' => 'message', 'label' => 'Message', 'required' => true, 'rows' => 4],
        ['type' => 'select', 'name' => 'image', 'label' => 'Image (pick existing)', 'options' => []],
        ['type' => 'checkbox', 'name' => 'visible', 'help' => 'Visible', 'value' => 1],
    ],

    'testimonials' => [
        ['type' => 'text', 'name' => 'name', 'label' => 'Name', 'required' => true],
        ['type' => 'text', 'name' => 'role', 'label' => 'Role/Title'],
        ['type' => 'textarea', 'name' => 'message', 'label' => 'Message', 'rows' => 4],
        ['type' => 'file', 'name' => 'image', 'label' => 'Image'],
        ['type' => 'checkbox', 'name' => 'visible', 'help' => 'Visible', 'value' => 1],
    ],

    'services' => [
        ['type' => 'text', 'name' => 'title', 'label' => 'Title', 'required' => true],
        ['type' => 'textarea', 'name' => 'description', 'label' => 'Description', 'rows' => 6],
        ['type' => 'text', 'name' => 'icon', 'label' => 'Icon/Class'],
        ['type' => 'file', 'name' => 'image', 'label' => 'Image'],
    ],

    'projects' => [
        ['type' => 'text', 'name' => 'title', 'label' => 'Title', 'required' => true],
        ['type' => 'textarea', 'name' => 'description', 'label' => 'Description', 'rows' => 6],
        ['type' => 'file', 'name' => 'image', 'label' => 'Image'],
        ['type' => 'text', 'name' => 'category', 'label' => 'Category'],
    ],

    'posts' => [
        ['type' => 'text', 'name' => 'title', 'label' => 'Title', 'required' => true],
        ['type' => 'text', 'name' => 'slug', 'label' => 'Slug (optional)'],
        ['type' => 'textarea', 'name' => 'excerpt', 'label' => 'Excerpt', 'rows' => 3],
        ['type' => 'textarea', 'name' => 'body', 'label' => 'Body', 'rows' => 8],
        ['type' => 'file', 'name' => 'image', 'label' => 'Image'],
        ['type' => 'text', 'name' => 'author', 'label' => 'Author'],
    ],

    'content' => [
        ['type' => 'text', 'name' => 'title', 'label' => 'Title'],
        ['type' => 'textarea', 'name' => 'body', 'label' => 'Body', 'rows' => 12],
        ['type' => 'file', 'name' => 'image', 'label' => 'Image'],
    ],
];
