import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/resources/views/frontend/checkout/index.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# Wrap the submit button in the sticky container
old_btn = r'<button type="submit" form="checkout-form" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow">(\s*Commander.*?\s*)</button>'
new_btn = r'<div class="mobile-sticky-checkout"><button type="submit" form="checkout-form" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow">\1</button></div>'

content = re.sub(old_btn, new_btn, content)

# Add script at the end to add body class
end_script = """
@push('scripts')
<script>
    document.body.classList.add('checkout-page');
</script>
@endpush
@endsection
"""

content = content.replace("@endsection", end_script)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("Checkout HTML updated for sticky mobile button")
