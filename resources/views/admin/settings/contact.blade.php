<div class="row">
    <div class="col-md-6">@include('admin.settings._field', ['key' => 'phone', 'label' => 'Phone', 'type' => 'tel'])</div>
    <div class="col-md-6">@include('admin.settings._field', ['key' => 'whatsapp', 'label' => 'WhatsApp number', 'placeholder' => '923001234567', 'hint' => 'Country code first, digits only. Powers the floating WhatsApp button.'])</div>
    <div class="col-md-6">@include('admin.settings._field', ['key' => 'email', 'label' => 'Email', 'type' => 'email'])</div>
    <div class="col-md-6">@include('admin.settings._field', ['key' => 'address', 'label' => 'Office address'])</div>
    <div class="col-12">
        @include('admin.settings._field', [
            'key' => 'whatsapp_message',
            'label' => 'WhatsApp message',
            'type' => 'textarea',
            'rows' => 2,
            'placeholder' => 'Hi, I am interested in {project} — {url}',
            'hint' => 'Written into the client\'s chat before they press send. {project} becomes the project or society name and {url} becomes its page link. Leave empty for the default.',
        ])
    </div>

    <div class="col-12">@include('admin.settings._field', ['key' => 'map_embed_url', 'label' => 'Office map', 'type' => 'textarea', 'rows' => 3, 'hint' => 'Google Maps → Share → Embed a map → Copy HTML, or just a plain map link. Loaded only after the visitor clicks.'])</div>
</div>
