@include('admin.settings._field', ['key' => 'about_heading', 'label' => 'Heading'])
@include('admin.settings._field', ['key' => 'about_content', 'label' => 'Content', 'type' => 'textarea', 'rows' => 12, 'hint' => 'Markdown supported: blank line for a new paragraph, ## for a sub-heading.'])
