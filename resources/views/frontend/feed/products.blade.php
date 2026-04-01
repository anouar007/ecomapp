<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title>{{ setting('app_name', 'Moubdi3oun') }}</title>
        <link>{{ url('/') }}</link>
        <description>{{ setting('app_description', 'Handcrafted Home Furniture') }}</description>
        @foreach($products as $product)
        <item>
            <g:id>{{ $product->id }}</g:id>
            <g:title><![CDATA[{{ $product->translated_name }}]]></g:title>
            <g:description><![CDATA[{{ Str::limit(strip_tags($product->translated_description), 5000) }}]]></g:description>
            <g:link>{{ route('shop.show', $product->id) }}</g:link>
            <g:image_link>{{ $product->main_image ? asset('storage/' . $product->main_image) : asset('images/placeholder.jpg') }}</g:image_link>
            <g:condition>new</g:condition>
            <g:availability>{{ $product->getTotalStockAttribute() > 0 ? 'in stock' : 'out of stock' }}</g:availability>
            <g:price>{{ $product->price }} MAD</g:price>
            @if($product->isOnSale())
            <g:sale_price>{{ $product->sale_price }} MAD</g:sale_price>
            @endif
            @if($product->review_count > 0)
            <g:review_count>{{ $product->review_count }}</g:review_count>
            <g:average_rating>{{ $product->average_rating }}</g:average_rating>
            @endif
            <g:brand>{{ setting('app_name', 'Moubdi3oun') }}</g:brand>
            <g:google_product_category>Furniture</g:google_product_category>
            <g:product_type><![CDATA[{{ $product->productCategory->translated_name ?? 'Furniture' }}]]></g:product_type>
            <g:shipping>
                <g:country>MA</g:country>
                <g:service>Standard</g:service>
                <g:price>0.00 MAD</g:price>
            </g:shipping>
        </item>
        @endforeach
    </channel>
</rss>
