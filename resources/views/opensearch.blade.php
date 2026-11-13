{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/"
                       xmlns:moz="http://www.mozilla.org/2006/browser/search/">
    <ShortName>WINA SHOP</ShortName>
    <Description>Rechercher caméras Sony FX3, Alpha A7, DJI Osmo Pocket 3, stabilisateurs, microphones, éclairage Godox et matériel cinéma au Maroc — WINA SHOP Casablanca</Description>
    <InputEncoding>UTF-8</InputEncoding>
    <OutputEncoding>UTF-8</OutputEncoding>
    <Language>fr-MA</Language>
    <Language>ar-MA</Language>
    <Language>en-MA</Language>
    <Image width="64" height="64" type="image/png">{{ $iconUrl }}</Image>
    <Url type="text/html" method="get" template="{{ $shopUrl }}?q={searchTerms}" />
    <Url type="application/json" template="{{ $liveSearchUrl }}?q={searchTerms}" />
    <Query role="example" searchTerms="Sony FX3" />
    <moz:SearchForm>{{ $shopUrl }}</moz:SearchForm>
</OpenSearchDescription>

