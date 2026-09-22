<div class="row">
    <div class="col-md-6">@include('admin.settings._field', ['key' => 'phone', 'label' => 'Phone', 'type' => 'tel'])</div>
    <div class="col-md-6">@include('admin.settings._field', ['key' => 'whatsapp', 'label' => 'WhatsApp number', 'placeholder' => '923001234567', 'hint' => 'Country code first, digits only. Powers the floating WhatsApp button.'])</div>
    <div class="col-md-6">@include('admin.settings._field', ['key' => 'email', 'label' => 'Email', 'type' => 'email'])</div>
    <div class="col-md-6">@include('admin.settings._field', ['key' => 'address', 'label' => 'Office address'])</div>
    <div class="col-12">@include('admin.settings._field', ['key' => 'map_embed_url', 'label' => 'Office map embed URL', 'type' => 'url', 'hint' => 'Loaded on the Contact page only, after the visitor clicks.'])</div>
</div>
