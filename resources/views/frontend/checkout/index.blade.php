@extends('layouts.frontend')

@section('meta_title', __('Order Confirmation') . ' — ' . setting('app_name', 'Moubdi3oun'))

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Premium Tom Select Overrides */
    .ts-wrapper.form-select {
        padding: 0 !important;
        background: none !important;
    }
    .ts-control {
        border-radius: 14px !important;
        padding: 1.1rem 1.25rem !important;
        padding-right: 3rem !important;
        border: 1px solid #e5e7eb !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        font-family: inherit !important;
        background-color: #fff !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23AF2926' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'%3E%3C/path%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 1.25rem center !important;
        background-size: 1.2rem !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 4px rgba(175, 41, 38, 0.1) !important;
    }
    .ts-dropdown {
        border-radius: 16px !important;
        margin-top: 10px !important;
        border: 1px solid rgba(0,0,0,0.05) !important;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15) !important;
        overflow: hidden !important;
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(10px) !important;
        animation: ts-dropdown-in 0.25s ease-out !important;
    }
    @keyframes ts-dropdown-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .ts-dropdown .active {
        background-color: var(--accent) !important;
        color: #fff !important;
    }
    .ts-dropdown .option {
        padding: 12px 20px !important;
        font-size: 0.95rem !important;
        transition: all 0.2s ease !important;
    }
    .ts-control input::placeholder {
        color: #9ca3af !important;
        opacity: 0.7 !important;
    }
    .ts-wrapper .ts-control input {
        font-size: 0.95rem !important;
    }
    .no-results {
        padding: 20px !important;
        text-align: center;
        color: var(--text-muted);
        font-style: italic;
    }
</style>
@endpush

@section('content')
<div class="bg-light section-py min-vh-100">
    <div class="container">
        <h1 class="fw-black mb-5 h2 border-start-primary ps-3 text-uppercase ls-1">{{ __('Checkout Title') }}</h1>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-black mb-4 h5 text-uppercase ls-1">{{ __('Shipping Info') }}</h4>
                        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                            @csrf
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">{{ __('Full Name') }}</label>
                                    <input type="text" name="customer_name" class="form-control bg-white border py-3 rounded-3" placeholder="{{ __('Name and Surname') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">{{ __('Email Label') }} ({{ __('Optional') }})</label>
                                    <input type="email" name="customer_email" class="form-control bg-white border py-3 rounded-3" placeholder="{{ __('Optional Track') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">{{ __('Phone Label') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border rounded-start-3" dir="ltr">+212</span>
                                        <input type="tel" name="customer_phone" class="form-control bg-white border py-3 rounded-end-3" 
                                               placeholder="6 XX XX XX XX" 
                                               pattern="[0-9]{9}" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">{{ __('Address Label') }}</label>
                                    <input type="text" name="shipping_address" class="form-control bg-white border py-3 rounded-3" placeholder="{{ __('Address Placeholder') }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-muted">{{ __('City Label') }}</label>
                                    <select name="shipping_city" class="form-select bg-white border py-3 rounded-3" required>
                                        @php
                                            $cities = [
                                                ["arabic" => "أدوز - بني ملال", "eng" => "Adouz - Beni Mellal"],
                                                ["arabic" => "أفورار", "eng" => "Afourar"],
                                                ["arabic" => "أفرا", "eng" => "AFRA"],
                                                ["arabic" => "أكادير", "eng" => "Agadir"],
                                                ["arabic" => "أكدز", "eng" => "Agdz"],
                                                ["arabic" => "أغبالو نكردان", "eng" => "Aghbalou Ncerdan"],
                                                ["arabic" => "أكلو", "eng" => "Aglou"],
                                                ["arabic" => "أكوراي", "eng" => "Agourai"],
                                                ["arabic" => "أكلموس", "eng" => "Aguelmous"],
                                                ["arabic" => "أحفير", "eng" => "AHFIR"],
                                                ["arabic" => "عين عيشة", "eng" => "Ain Aicha"],
                                                ["arabic" => "عين الله", "eng" => "Ain Allah"],
                                                ["arabic" => "عين عتيق", "eng" => "Ain Attig"],
                                                ["arabic" => "عين بيضاء - وزان", "eng" => "Ain Beida - Ouazzane"],
                                                ["arabic" => "عين بني مظهر", "eng" => "Ain Beni Mathar"],
                                                ["arabic" => "عين بيضاء", "eng" => "Ain Bida"],
                                                ["arabic" => "عين الشكاك", "eng" => "Ain Cheggag"],
                                                ["arabic" => "عين الشقف - فاس", "eng" => "Ain Chkef - FES"],
                                                ["arabic" => "عين دفالي", "eng" => "Ain Defali"],
                                                ["arabic" => "عين دريج", "eng" => "Ain dorij"],
                                                ["arabic" => "عين العودة", "eng" => "Ain El Aouda"],
                                                ["arabic" => "عين الركادة", "eng" => "Ain Erreggada"],
                                                ["arabic" => "عين حرودة", "eng" => "Ain Harrouda"],
                                                ["arabic" => "عين جيري - مكناس", "eng" => "Ain jiri - meknes"],
                                                ["arabic" => "عين قنصرة", "eng" => "Ain Kansara"],
                                                ["arabic" => "عين كرمة", "eng" => "AIN KARMA"],
                                                ["arabic" => "عين اللوح", "eng" => "Ain Leuh"],
                                                ["arabic" => "عين مديونة - تاونات", "eng" => "Ain Mediouna - Taounate"],
                                                ["arabic" => "عين الركادة", "eng" => "Ain Reggada"],
                                                ["arabic" => "عين تاوجطات", "eng" => "Ain Taoujdate"],
                                                ["arabic" => "آيت علي", "eng" => "Ait Ali"],
                                                ["arabic" => "آيت علي أزرو", "eng" => "Ait Ali Azrou"],
                                                ["arabic" => "آيت عميرة", "eng" => "Ait Amira"],
                                                ["arabic" => "آيت باها", "eng" => "Ait Baha"],
                                                ["arabic" => "آيت بلقاسم", "eng" => "Ait Belkacem"],
                                                ["arabic" => "آيت إسحاق", "eng" => "Ait Ishaq"],
                                                ["arabic" => "آيت ملول", "eng" => "Ait melloul"],
                                                ["arabic" => "آيت ورير", "eng" => "Ait Ourir"],
                                                ["arabic" => "آيت الربع", "eng" => "Ait Rbaa"],
                                                ["arabic" => "آيت سعيد", "eng" => "Ait Said"],
                                                ["arabic" => "آيت يعزم", "eng" => "AIT YAAZEM"],
                                                ["arabic" => "آيت يوسف", "eng" => "Ait youssef"],
                                                ["arabic" => "أجدير", "eng" => "Ajdir"],
                                                ["arabic" => "أقشور", "eng" => "Akchour"],
                                                ["arabic" => "أخفنير", "eng" => "AKHFNIR"],
                                                ["arabic" => "أقا", "eng" => "Akka"],
                                                ["arabic" => "أقا إيغان", "eng" => "Akka Ighane"],
                                                ["arabic" => "أقليم", "eng" => "Aklim"],
                                                ["arabic" => "أكنول", "eng" => "Aknoul"],
                                                ["arabic" => "العروي", "eng" => "Al Aaroui"],
                                                ["arabic" => "الحسيمة", "eng" => "Al Hoceima"],
                                                ["arabic" => "الخلفية", "eng" => "Al Khalfia"],
                                                ["arabic" => "المرابطين", "eng" => "Al Morabitin"],
                                                ["arabic" => "المرابطين", "eng" => "Al Morabitin"],
                                                ["arabic" => "المرابطين", "eng" => "Al Morabitin"],
                                                ["arabic" => "الماص", "eng" => "Almaz"],
                                                ["arabic" => "ألنيف", "eng" => "Alnif"],
                                                ["arabic" => "أمتراس", "eng" => "Ametrass"],
                                                ["arabic" => "أمزميز", "eng" => "Amizmiz"],
                                                ["arabic" => "أمسا", "eng" => "Amsa"],
                                                ["arabic" => "أنزا", "eng" => "Anza"],
                                                ["arabic" => "أوفوس", "eng" => "Aoufous"],
                                                ["arabic" => "أولوز", "eng" => "Aoulouz"],
                                                ["arabic" => "أورير", "eng" => "Aourir"],
                                                ["arabic" => "أربعاء عونات", "eng" => "Arba Aounate"],
                                                ["arabic" => "أصيلة", "eng" => "Asilah"],
                                                ["arabic" => "أسني", "eng" => "Asni"],
                                                ["arabic" => "آسا", "eng" => "Assa"],
                                                ["arabic" => "آيت يحيى أو علا", "eng" => "Ayt Yahya Oalla"],
                                                ["arabic" => "أزمور", "eng" => "Azemmour"],
                                                ["arabic" => "أزيلال", "eng" => "Azilal"],
                                                ["arabic" => "أزلا", "eng" => "Azla"],
                                                ["arabic" => "أزرو", "eng" => "AZROU"],
                                                ["arabic" => "عزابة - صفرو", "eng" => "Azzaba - Sefrou"],
                                                ["arabic" => "باب برد", "eng" => "Bab Berred"],
                                                ["arabic" => "باب مرزوقة", "eng" => "Bab Marzouka"],
                                                ["arabic" => "باب تازة", "eng" => "Bab Taza"],
                                                ["arabic" => "باريشينو", "eng" => "Barichino"],
                                                ["arabic" => "أبي الجعد", "eng" => "Bejaad"],
                                                ["arabic" => "بلعكيد", "eng" => "Belaaguid"],
                                                ["arabic" => "بلفاع", "eng" => "Belfaa"],
                                                ["arabic" => "بليونش", "eng" => "Belyounech"],
                                                ["arabic" => "بن أحمد", "eng" => "Ben ahmed"],
                                                ["arabic" => "بن جرير", "eng" => "Ben Guerir"],
                                                ["arabic" => "بن الطيب", "eng" => "Ben Taieb"],
                                                ["arabic" => "بن يخلف", "eng" => "Ben Yakhlef"],
                                                ["arabic" => "بني أحمد - شفشاون", "eng" => "Beni Ahmed - Chefchaouen"],
                                                ["arabic" => "بني شيكر", "eng" => "Beni Chiker"],
                                                ["arabic" => "بني درار", "eng" => "Beni Drar"],
                                                ["arabic" => "بني نصار", "eng" => "Beni Ensar"],
                                                ["arabic" => "بني ملال", "eng" => "Beni Mellal"],
                                                ["arabic" => "بني وليد", "eng" => "Beni Oulid"],
                                                ["arabic" => "بني سيدال الجبل", "eng" => "Beni Sidal Jbel"],
                                                ["arabic" => "بنسليمان", "eng" => "Benslimane"],
                                                ["arabic" => "بركان", "eng" => "Berkane"],
                                                ["arabic" => "برشيد", "eng" => "Berrchid"],
                                                ["arabic" => "بهاليل", "eng" => "Bhalil"],
                                                ["arabic" => "بيوكرى", "eng" => "Biougra"],
                                                ["arabic" => "بئر جديد", "eng" => "Bir Jdid"],
                                                ["arabic" => "بئر طم طم", "eng" => "Bir Tam Tam"],
                                                ["arabic" => "بيركوات", "eng" => "Birkouate"],
                                                ["arabic" => "بيزداد", "eng" => "Bizdad"],
                                                ["arabic" => "بني عياط", "eng" => "Bni Ayat"],
                                                ["arabic" => "بني بوعياش", "eng" => "Bni Bouayach"],
                                                ["arabic" => "بني رزين", "eng" => "Bni Rzine"],
                                                ["arabic" => "بوعنان تطوان", "eng" => "bouanane tetouan"],
                                                ["arabic" => "بوعرفة", "eng" => "Bouarfa"],
                                                ["arabic" => "بوعرك", "eng" => "Bouarg"],
                                                ["arabic" => "بودربالة", "eng" => "BOUDERBALA"],
                                                ["arabic" => "بودينار", "eng" => "Boudinar"],
                                                ["arabic" => "بودنيب", "eng" => "Boudnib"],
                                                ["arabic" => "بوفكران", "eng" => "Boufakrane"],
                                                ["arabic" => "بوغريبة", "eng" => "Boughriba"],
                                                ["arabic" => "بوهودة", "eng" => "Bouhouda"],
                                                ["arabic" => "بويزكارن", "eng" => "Bouizakarne"],
                                                ["arabic" => "بوجدور", "eng" => "Boujdour"],
                                                ["arabic" => "بوجنيبة", "eng" => "Boujniba"],
                                                ["arabic" => "بولعلام", "eng" => "Boulaalam"],
                                                ["arabic" => "بولنوار", "eng" => "Boulanouare"],
                                                ["arabic" => "بولمان", "eng" => "Boulemane"],
                                                ["arabic" => "بومالن دادس", "eng" => "Boumaln dads"],
                                                ["arabic" => "بومية", "eng" => "Boumia"],
                                                ["arabic" => "بونعمان - تيزنيت", "eng" => "Bounaamane - Tiznit"],
                                                ["arabic" => "بوسكورة", "eng" => "Bouskoura"],
                                                ["arabic" => "بوزغلال", "eng" => "Bouzaghlal"],
                                                ["arabic" => "بوزنيقة", "eng" => "Bouznika"],
                                                ["arabic" => "بريدية", "eng" => "BRIDIA"],
                                                ["arabic" => "بريكشة", "eng" => "Brikcha"],
                                                ["arabic" => "كابو نيكرو", "eng" => "Cabo Negro"],
                                                ["arabic" => "الدار البيضاء", "eng" => "Casablanca"],
                                                ["arabic" => "شفشاون", "eng" => "Chefchaouen"],
                                                ["arabic" => "الشلالات", "eng" => "Chellalat"],
                                                ["arabic" => "شيشاوة", "eng" => "Chichaoua"],
                                                ["arabic" => "شويتر", "eng" => "Chouiter"],
                                                ["arabic" => "شريفية", "eng" => "Chrifia"],
                                                ["arabic" => "الداخلة", "eng" => "Dakhla"],
                                                ["arabic" => "دالية", "eng" => "Dalia"],
                                                ["arabic" => "دار 16", "eng" => "Dar 16"],
                                                ["arabic" => "دار أقوباع", "eng" => "Dar Akoubaa"],
                                                ["arabic" => "دار بوعزة", "eng" => "Dar Bouazza"],
                                                ["arabic" => "دار الكبداني", "eng" => "Dar El Kebdani"],
                                                ["arabic" => "دار ولد زيدوح", "eng" => "Dar Ould Zidouh"],
                                                ["arabic" => "دردارة", "eng" => "Dardara"],
                                                ["arabic" => "دشيرة", "eng" => "Dcheira"],
                                                ["arabic" => "دمنات", "eng" => "Demnate"],
                                                ["arabic" => "الدروة", "eng" => "Deroua"],
                                                ["arabic" => "دخيسة", "eng" => "DKHISSA"],
                                                ["arabic" => "دلالحة", "eng" => "Dlalha"],
                                                ["arabic" => "دوار بوعزة", "eng" => "Douar Bouazza"],
                                                ["arabic" => "دوار لعراب", "eng" => "Douar Laareb"],
                                                ["arabic" => "دوار سيدي موسى", "eng" => "Douar Sidi Moussa"],
                                                ["arabic" => "دوار سلطان", "eng" => "Douar Soultan"],
                                                ["arabic" => "دوويات - فاس", "eng" => "Douiyat - FES"],
                                                ["arabic" => "دراركة", "eng" => "Drarga"],
                                                ["arabic" => "الدريوش", "eng" => "Driouch"],
                                                ["arabic" => "الشماعية", "eng" => "Echemmaia"],
                                                ["arabic" => "العنصر", "eng" => "El aanssar"],
                                                ["arabic" => "العيون سيدي ملوك", "eng" => "El Aioun Sidi Mellouk"],
                                                ["arabic" => "العوامرة", "eng" => "El Aouamra"],
                                                ["arabic" => "العرجات", "eng" => "El Arjate"],
                                                ["arabic" => "العطاوية", "eng" => "El Attaouia"],
                                                ["arabic" => "البركة - مكناس", "eng" => "EL BARAKA - MEKNES"],
                                                ["arabic" => "البرج - خنيفرة", "eng" => "El Borj - Khenifra"],
                                                ["arabic" => "البروج", "eng" => "El Borouj"],
                                                ["arabic" => "الكارة", "eng" => "El Gara"],
                                                ["arabic" => "الحنشان", "eng" => "El Hanchane"],
                                                ["arabic" => "الحوزية", "eng" => "El Haouzia"],
                                                ["arabic" => "الحرارتة", "eng" => "El Hrarta"],
                                                ["arabic" => "الجديدة", "eng" => "El Jadida"],
                                                ["arabic" => "الجبهة", "eng" => "El Jebeha"],
                                                ["arabic" => "القباب", "eng" => "El Kebab"],
                                                ["arabic" => "قلعة السراغنة", "eng" => "El Kelaa des Sraghna"],
                                                ["arabic" => "الخميس آيت واحي", "eng" => "El Khemis des Aït Ouahi"],
                                                ["arabic" => "القصيبة - بني ملال", "eng" => "El Ksiba - Beni-Mellal"],
                                                ["arabic" => "المنصورية", "eng" => "El Mansouria"],
                                                ["arabic" => "المرسى العيون", "eng" => "EL MARSA LAAYOUNE"],
                                                ["arabic" => "المنزل - صفرو", "eng" => "El Menzel - SEFROU"],
                                                ["arabic" => "الوطية طانطان", "eng" => "El Ouatia TAN TAN"],
                                                ["arabic" => "الحاجب", "eng" => "ELHajeb"],
                                                ["arabic" => "أرفود", "eng" => "Erfoud"],
                                                ["arabic" => "الرشيدية", "eng" => "Errachidia"],
                                                ["arabic" => "الرحمة", "eng" => "Errahma"],
                                                ["arabic" => "الصويرة", "eng" => "Essaouira"],
                                                ["arabic" => "السمارة", "eng" => "Essmara SAHARA"],
                                                ["arabic" => "فج الريح", "eng" => "Faj Errih"],
                                                ["arabic" => "فم الحصن", "eng" => "Fam El Hisn"],
                                                ["arabic" => "فرخانة", "eng" => "Farkhana"],
                                                ["arabic" => "فاس", "eng" => "FES"],
                                                ["arabic" => "فكيك", "eng" => "Figuig"],
                                                ["arabic" => "الفنيدق", "eng" => "Fnideq"],
                                                ["arabic" => "فم العنصر", "eng" => "Foum El Ansar"],
                                                ["arabic" => "فم اودي - بني ملال", "eng" => "Foum Oudi - Beni Mellal"],
                                                ["arabic" => "فم زكيد", "eng" => "Foum zguid"],
                                                ["arabic" => "الفقيه بن صالح", "eng" => "Fquih Ben Salah"],
                                                ["arabic" => "فريشة - تاونات", "eng" => "Fricha - Taounate"],
                                                ["arabic" => "كلاز - تاونات", "eng" => "Galaz - Taounate"],
                                                ["arabic" => "غفساي", "eng" => "Ghafsai"],
                                                ["arabic" => "غزوة", "eng" => "Ghazoua"],
                                                ["arabic" => "قواسم", "eng" => "Gouassem"],
                                                ["arabic" => "كلميمة - الرشيدية", "eng" => "Goulmima - Errachidia"],
                                                ["arabic" => "كرامة", "eng" => "Gourrama"],
                                                ["arabic" => "كلميم", "eng" => "Guelmim"],
                                                ["arabic" => "جرسيف", "eng" => "Guercif"],
                                                ["arabic" => "كزناية", "eng" => "Gueznaia"],
                                                ["arabic" => "كيكو", "eng" => "Guigou"],
                                                ["arabic" => "حد بوموسى - بني ملال", "eng" => "Had Boumoussa - - Beni Mellal"],
                                                ["arabic" => "حد الدرى", "eng" => "Had Draa"],
                                                ["arabic" => "حد حرارة - آسفي", "eng" => "Had Hrara- Safi"],
                                                ["arabic" => "حد السوالم", "eng" => "had soualem"],
                                                ["arabic" => "حاج قدور", "eng" => "Haj Kaddour"],
                                                ["arabic" => "هرهورة", "eng" => "Harhoura"],
                                                ["arabic" => "حطان", "eng" => "Hattane"],
                                                ["arabic" => "هوارة", "eng" => "Houara"],
                                                ["arabic" => "حرارة آسفي", "eng" => "Hrara Safi"],
                                                ["arabic" => "إفران", "eng" => "IFRANE"],
                                                ["arabic" => "اغرم لعلام", "eng" => "Ighrem Laalam"],
                                                ["arabic" => "إيمي ودار", "eng" => "Imi Ouaddar"],
                                                ["arabic" => "إملشيل", "eng" => "Imilchil"],
                                                ["arabic" => "إمنتانوت", "eng" => "imintanoute"],
                                                ["arabic" => "إيموزار مرموشة", "eng" => "Imouzzer Marmoucha"],
                                                ["arabic" => "إيموزار كندر - فاس", "eng" => "Imouzzer-Kandar - FES"],
                                                ["arabic" => "إمزورن", "eng" => "Imzouren"],
                                                ["arabic" => "إنشادن", "eng" => "Inchaden"],
                                                ["arabic" => "إنزكان", "eng" => "Inezgane"],
                                                ["arabic" => "إساكن", "eng" => "Issaguen"],
                                                ["arabic" => "جعدار", "eng" => "Jaadar"],
                                                ["arabic" => "جمعة حودران", "eng" => "Jamaa Houderrane"],
                                                ["arabic" => "جمعة سحيم", "eng" => "Jamaat shaim"],
                                                ["arabic" => "جرادة", "eng" => "Jerada"],
                                                ["arabic" => "جرف الملحة", "eng" => "Jorf El Melha"],
                                                ["arabic" => "جرف الأصفر", "eng" => "Jorf Lasfar"],
                                                ["arabic" => "قاع أسراس", "eng" => "kaa asras"],
                                                ["arabic" => "كابيلا", "eng" => "Kabila"],
                                                ["arabic" => "كاف نسور", "eng" => "Kaf Nsour"],
                                                ["arabic" => "قنطرة العسكر", "eng" => "Kantra El Ascar"],
                                                ["arabic" => "قرية أركمان", "eng" => "Kariat Arekmane"],
                                                ["arabic" => "قرية با محمد", "eng" => "Kariat Ba Mohamed"],
                                                ["arabic" => "قصبة تادلة", "eng" => "Kasba Tadla"],
                                                ["arabic" => "قصبة الطاهر", "eng" => "Kasbah El Taher"],
                                                ["arabic" => "كاسيطا", "eng" => "Kassita"],
                                                ["arabic" => "كشولة", "eng" => "Kchoula"],
                                                ["arabic" => "قلعة مكونة", "eng" => "Kelaat MGouna"],
                                                ["arabic" => "القنيطرة", "eng" => "Kenitra"],
                                                ["arabic" => "خميس المضيق", "eng" => "Khamis Mdiq"],
                                                ["arabic" => "خميس زمامرة", "eng" => "Khemis Zemamera"],
                                                ["arabic" => "الخميسات", "eng" => "KHEMISSET"],
                                                ["arabic" => "خنيشات", "eng" => "Khenichet"],
                                                ["arabic" => "خنيفرة", "eng" => "Khenifra"],
                                                ["arabic" => "خلالفة", "eng" => "khlalfa"],
                                                ["arabic" => "خريبكة", "eng" => "Khouribga"],
                                                ["arabic" => "كريمة", "eng" => "Kraymat"],
                                                ["arabic" => "القصر الكبير", "eng" => "Ksar El Kebir"],
                                                ["arabic" => "القصر الصغير", "eng" => "Ksar Sghir"],
                                                ["arabic" => "المدينة الخضراء", "eng" => "La ville verte"],
                                                ["arabic" => "العيايطة - بني ملال", "eng" => "Laayayta - Beni Mellal"],
                                                ["arabic" => "العيون الشرقية", "eng" => "Laayoune Charkia"],
                                                ["arabic" => "ميناء العيون", "eng" => "laayoune Port"],
                                                ["arabic" => "العيون الصحراء", "eng" => "Laayoune Sahara"],
                                                ["arabic" => "الكفيفات", "eng" => "Lagfifat"],
                                                ["arabic" => "الهراويين", "eng" => "Lahraouyine"],
                                                ["arabic" => "لهري - خنيفرة", "eng" => "Lahri - khenifra"],
                                                ["arabic" => "الخيايطة", "eng" => "Lakhyayeta"],
                                                ["arabic" => "للا ميمونة", "eng" => "Lalla mimouna"],
                                                ["arabic" => "للا تاكركوست", "eng" => "Lalla Takerkoust"],
                                                ["arabic" => "لمكانسة - الدار البيضاء", "eng" => "Lamkansa - Casa"],
                                                ["arabic" => "العرائش", "eng" => "Larache"],
                                                ["arabic" => "القليعة", "eng" => "Leqliaa"],
                                                ["arabic" => "الخيايطة - حد السوالم", "eng" => "Lkhyayta - Had soualem"],
                                                ["arabic" => "لوداية", "eng" => "loudaya"],
                                                ["arabic" => "لويزية", "eng" => "Louizia"],
                                                ["arabic" => "الويحة", "eng" => "Lwaiha"],
                                                ["arabic" => "المضيق", "eng" => "M Diq"],
                                                ["arabic" => "معازيز", "eng" => "Maaziz"],
                                                ["arabic" => "مداغ", "eng" => "Madagh"],
                                                ["arabic" => "مارينا سمير", "eng" => "Marina smir"],
                                                ["arabic" => "ماريواري", "eng" => "Mariouari"],
                                                ["arabic" => "مراكش", "eng" => "Marrakech"],
                                                ["arabic" => "مرتيل", "eng" => "Martil"],
                                                ["arabic" => "مرزوكة", "eng" => "Marzouga"],
                                                ["arabic" => "مصمودة", "eng" => "Masmouda"],
                                                ["arabic" => "ماسة", "eng" => "Massa"],
                                                ["arabic" => "مشرع بلقصيري", "eng" => "Mechra Bel Ksiri"],
                                                ["arabic" => "مديونة", "eng" => "Mediouna"],
                                                ["arabic" => "المهدية", "eng" => "Mehdia"],
                                                ["arabic" => "مجاط - مراكش", "eng" => "Mejjat - Marrakech"],
                                                ["arabic" => "مجاط - مكناس", "eng" => "MEJJAT - Meknes"],
                                                ["arabic" => "ميجي", "eng" => "Mejji"],
                                                ["arabic" => "مكناس", "eng" => "MEKNES"],
                                                ["arabic" => "مرنيسة", "eng" => "Mernissa"],
                                                ["arabic" => "مرس الخير", "eng" => "Mers El Kheir"],
                                                ["arabic" => "مسكالة", "eng" => "Meskala"],
                                                ["arabic" => "مهاية - مكناس", "eng" => "MHAYA - MEKNES"],
                                                ["arabic" => "ميضار", "eng" => "Midar"],
                                                ["arabic" => "ميدلت", "eng" => "Midelt"],
                                                ["arabic" => "ميرلفت", "eng" => "Mirleft"],
                                                ["arabic" => "ميسور", "eng" => "Missour"],
                                                ["arabic" => "المحمدية", "eng" => "Mohammedia"],
                                                ["arabic" => "مقريصات", "eng" => "Mokrisset"],
                                                ["arabic" => "مولاي عبد الله أمغار", "eng" => "Moulay Abellah Amghar"],
                                                ["arabic" => "مولاي بوعزة", "eng" => "Moulay Bouazza"],
                                                ["arabic" => "مولاي بوسلهام", "eng" => "Moulay Bousselham"],
                                                ["arabic" => "مولاي إبراهيم", "eng" => "Moulay Brahim"],
                                                ["arabic" => "مولاي إدريس زرهون", "eng" => "Moulay Idriss Zerhoun"],
                                                ["arabic" => "مولاي يعقوب", "eng" => "Moulay Yacoub"],
                                                ["arabic" => "مقام الطلبة", "eng" => "Mqam Tolba"],
                                                ["arabic" => "مرامر", "eng" => "Mramer"],
                                                ["arabic" => "مريرت", "eng" => "Mrirt"],
                                                ["arabic" => "أمسمرير تنغير", "eng" => "Msemrir tinghir"],
                                                ["arabic" => "مزودية", "eng" => "Mzoudia"],
                                                ["arabic" => "الناظور", "eng" => "NADOR"],
                                                ["arabic" => "النكوب زاكورة", "eng" => "Nkoub Zagora"],
                                                ["arabic" => "النواصر", "eng" => "Nouaceur"],
                                                ["arabic" => "نزالة لعضم", "eng" => "Nzalat Laadam"],
                                                ["arabic" => "واحة سيدي ابراهيم", "eng" => "Ouahat Sidi Brahim"],
                                                ["arabic" => "الوليدية", "eng" => "Oualidia"],
                                                ["arabic" => "واويزغت", "eng" => "Ouaouizeght"],
                                                ["arabic" => "واومانا", "eng" => "Ouaoumana"],
                                                ["arabic" => "ورزازات", "eng" => "Ouarzazate"],
                                                ["arabic" => "وزان", "eng" => "Ouazzane"],
                                                ["arabic" => "الوداية مراكش", "eng" => "OUDAYA Marrakech"],
                                                ["arabic" => "وادي أمليل", "eng" => "Oued Amlil"],
                                                ["arabic" => "وادي الجديدة", "eng" => "OUED JDIDA"],
                                                ["arabic" => "وادي لاو", "eng" => "Oued Laou"],
                                                ["arabic" => "وادي زم", "eng" => "Oued Zem"],
                                                ["arabic" => "أوكمس", "eng" => "Ougmas"],
                                                ["arabic" => "ويدان مراكش", "eng" => "Ouidane MARRAKECH"],
                                                ["arabic" => "ويسلان", "eng" => "OUISSLAN"],
                                                ["arabic" => "وجدة", "eng" => "Oujda"],
                                                ["arabic" => "أولاد علي - بني ملال", "eng" => "Oulad Ali - Beni Mellal"],
                                                ["arabic" => "أولاد عمران", "eng" => "Oulad Amrane"],
                                                ["arabic" => "أولاد عياد", "eng" => "Oulad Ayad"],
                                                ["arabic" => "أولاد برحيل", "eng" => "Oulad Berhil"],
                                                ["arabic" => "أولاد داحو", "eng" => "Oulad Dahou"],
                                                ["arabic" => "أولاد فرج", "eng" => "Oulad Frej"],
                                                ["arabic" => "أولاد غانم", "eng" => "Oulad Ghanem"],
                                                ["arabic" => "أولاد حسون", "eng" => "OULAD HASSOUN"],
                                                ["arabic" => "أولاد إسماعيل - بني ملال", "eng" => "Oulad ismail - Beni Mellal"],
                                                ["arabic" => "أولاد الطيب - فاس", "eng" => "Oulad Tayeb - FES"],
                                                ["arabic" => "أولاد تايمة", "eng" => "Oulad Teima"],
                                                ["arabic" => "أولاد يحيى", "eng" => "Oulad yahya"],
                                                ["arabic" => "أولاد يعيش", "eng" => "Oulad Yaich"],
                                                ["arabic" => "أولاد يوسف - بني ملال", "eng" => "Oulad Youssef - Beni Mellal"],
                                                ["arabic" => "أولاد زمام", "eng" => "Oulad zmam"],
                                                ["arabic" => "أولاد بن رحمون", "eng" => "OULED BEN RAHMOUN"],
                                                ["arabic" => "أولاد داود - تاونات", "eng" => "Ouled Daoud - Taounate"],
                                                ["arabic" => "أولاد إدريس - بني ملال", "eng" => "Ouled Driss - Beni Mellal"],
                                                ["arabic" => "أولاد مبارك", "eng" => "Ouled Mbarek"],
                                                ["arabic" => "أولاد موسى", "eng" => "Ouled Moussa"],
                                                ["arabic" => "أولاد مراح", "eng" => "Ouled Mrah"],
                                                ["arabic" => "أولاد نمة", "eng" => "Ouled Nemma"],
                                                ["arabic" => "أولاد سعيد الواد", "eng" => "Ouled Said El Oued"],
                                                ["arabic" => "أولاد ستوت", "eng" => "Ouled Settout"],
                                                ["arabic" => "أولماس", "eng" => "Oulmes"],
                                                ["arabic" => "أوناغة", "eng" => "Ounagha"],
                                                ["arabic" => "أونانة", "eng" => "Ounnana"],
                                                ["arabic" => "أوريكا", "eng" => "Ourika"],
                                                ["arabic" => "أورتزاغ - تاونات", "eng" => "Ourtzagh - Taounate"],
                                                ["arabic" => "أوطاط الحاج", "eng" => "Outat El Haj"],
                                                ["arabic" => "شاطئ دافيد بوزنيقة", "eng" => "Plage David Bouznika"],
                                                ["arabic" => "الرباط", "eng" => "RABAT"],
                                                ["arabic" => "رأس الماء - الناظور", "eng" => "Ras El Ma - Nador"],
                                                ["arabic" => "رأس الماء - فاس", "eng" => "Ras El Ma-FES"],
                                                ["arabic" => "رأس تابودة", "eng" => "Ras Tabouda"],
                                                ["arabic" => "رباط الخير", "eng" => "Ribate El Kheir"],
                                                ["arabic" => "الريش", "eng" => "Riche"],
                                                ["arabic" => "الريساني", "eng" => "Rissani"],
                                                ["arabic" => "الرماني", "eng" => "Rommani"],
                                                ["arabic" => "السعيدية", "eng" => "SAAIDIA"],
                                                ["arabic" => "سبع عيون - مكناس", "eng" => "Sabaâ Aïyoun - MEKNES"],
                                                ["arabic" => "آسفي", "eng" => "Safi"],
                                                ["arabic" => "سهل بوطاهر - تاونات", "eng" => "Sahel Boutaher - Taounate"],
                                                ["arabic" => "السعيدية", "eng" => "Saidia"],
                                                ["arabic" => "سلا الجديدة", "eng" => "Sala Al Jadida"],
                                                ["arabic" => "سلا", "eng" => "Sale"],
                                                ["arabic" => "سبت الكردان", "eng" => "Sebt El Guerdane"],
                                                ["arabic" => "سبت جزولة", "eng" => "Sebt Gzoula"],
                                                ["arabic" => "سبت متيوة", "eng" => "sebt mtiwa"],
                                                ["arabic" => "سبت سايس", "eng" => "Sebt Saiss"],
                                                ["arabic" => "صفرو", "eng" => "Sefrou"],
                                                ["arabic" => "زغنغن", "eng" => "Segangan"],
                                                ["arabic" => "سلوان", "eng" => "Selouane"],
                                                ["arabic" => "صنهاجة - صفرو", "eng" => "Senhaja - Sefrou"],
                                                ["arabic" => "سطات", "eng" => "Settat"],
                                                ["arabic" => "سيدي المختار", "eng" => "Sid L Mokhtar"],
                                                ["arabic" => "سيدي زوين", "eng" => "Sid Zouine"],
                                                ["arabic" => "سيدي عباد", "eng" => "Sidi Abbad"],
                                                ["arabic" => "سيدي عبد الله غياث", "eng" => "Sidi Abdellah Ghiat"],
                                                ["arabic" => "سيدي عبد الرزاق خزازنة", "eng" => "Sidi Abderrazzak Khzazna"],
                                                ["arabic" => "سيدي عدي", "eng" => "Sidi Addi"],
                                                ["arabic" => "سيدي عيسى بن علي", "eng" => "Sidi Aissa Ben Ali"],
                                                ["arabic" => "سيدي علي أزمور", "eng" => "Sidi Ali Azemmour"],
                                                ["arabic" => "سيدي علال البحراوي", "eng" => "Sidi Allal El Bahraoui"],
                                                ["arabic" => "سيدي علال التازي", "eng" => "Sidi Allal Tazi"],
                                                ["arabic" => "سيدي بنور", "eng" => "Sidi Bennour"],
                                                ["arabic" => "سيدي بيبي", "eng" => "Sidi bibi"],
                                                ["arabic" => "سيدي بو عثمان", "eng" => "Sidi Bou Othmane"],
                                                ["arabic" => "سيدي بوخلخال", "eng" => "Sidi Boukhalkhal"],
                                                ["arabic" => "سيدي بوقنادل", "eng" => "Sidi Bouknadel"],
                                                ["arabic" => "سيدي بولعلام", "eng" => "Sidi Boulaalam"],
                                                ["arabic" => "سيدي بوبر", "eng" => "Sidi Bousber"],
                                                ["arabic" => "سيدي بوزيد الجديدة", "eng" => "Sidi Bouzid El Jadida"],
                                                ["arabic" => "سيدي بوزيد آسفي", "eng" => "Sidi Bouzid SAFI"],
                                                ["arabic" => "سيدي حجاج", "eng" => "Sidi Hajjaj"],
                                                ["arabic" => "سيدي حرازم", "eng" => "Sidi Harazem"],
                                                ["arabic" => "سيدي إفني", "eng" => "SIDI IFNI"],
                                                ["arabic" => "سيدي جابر", "eng" => "Sidi Jaber"],
                                                ["arabic" => "سيدي قاسم", "eng" => "Sidi Kacem"],
                                                ["arabic" => "سيدي كاوكي", "eng" => "Sidi Kaouki"],
                                                ["arabic" => "سيدي مسعود", "eng" => "Sidi Massoud"],
                                                ["arabic" => "سيدي رحال", "eng" => "Sidi Rahal"],
                                                ["arabic" => "سيدي رضوان", "eng" => "Sidi Redouane"],
                                                ["arabic" => "سيدي سليمان", "eng" => "Sidi slimane"],
                                                ["arabic" => "سيدي طيبي", "eng" => "Sidi Taibi"],
                                                ["arabic" => "سيدي يحيى الغرب", "eng" => "Sidi Yahya El Gharb"],
                                                ["arabic" => "سيدي يحيى زعير", "eng" => "Sidi Yahya Zaer"],
                                                ["arabic" => "سخينات", "eng" => "Skhinate"],
                                                ["arabic" => "الصخيرات", "eng" => "Skhirat"],
                                                ["arabic" => "صخور الرحامنة", "eng" => "Skhour Rehamna"],
                                                ["arabic" => "السمارة", "eng" => "Smara"],
                                                ["arabic" => "سميمو", "eng" => "Smimou"],
                                                ["arabic" => "سويحلة", "eng" => "Souihla"],
                                                ["arabic" => "سوق الكور", "eng" => "SOUK EL GOUR"],
                                                ["arabic" => "سوق الأحد برادية", "eng" => "Souk El Had Des Bradia"],
                                                ["arabic" => "سوق خميس الساحل", "eng" => "Souk Khemis du Sahel"],
                                                ["arabic" => "سوق الأربعاء", "eng" => "SOUK LARBAA"],
                                                ["arabic" => "سوق السبت أولاد نمة", "eng" => "Souk Sebt Oulad Nemma"],
                                                ["arabic" => "سوق اثنين مكن", "eng" => "Souk Tnine Moghane"],
                                                ["arabic" => "إسليحات", "eng" => "Stehat"],
                                                ["arabic" => "تاشرافت", "eng" => "Tachrafat"],
                                                ["arabic" => "تدارت - أكادير", "eng" => "Taddart - Agadir"],
                                                ["arabic" => "تدارت - تازة", "eng" => "Taddart - Taza"],
                                                ["arabic" => "تفرسيت", "eng" => "Tafersit"],
                                                ["arabic" => "تفتشت", "eng" => "Tafetachte"],
                                                ["arabic" => "تفوغالت", "eng" => "Tafoughalt"],
                                                ["arabic" => "تافراوت", "eng" => "Tafraoute"],
                                                ["arabic" => "تغازوت", "eng" => "Taghazout"],
                                                ["arabic" => "تاكونيت زاكورة", "eng" => "Tagounite zagora"],
                                                ["arabic" => "تاكزيرت", "eng" => "Tagzert"],
                                                ["arabic" => "تحناوت", "eng" => "Tahannaout"],
                                                ["arabic" => "تاهلة", "eng" => "TAHLA"],
                                                ["arabic" => "تاليوين", "eng" => "Taliouine"],
                                                ["arabic" => "تلمست", "eng" => "Talmest"],
                                                ["arabic" => "تمنار", "eng" => "Tamanar"],
                                                ["arabic" => "تامنصورت", "eng" => "Tamansourt"],
                                                ["arabic" => "تاماريس", "eng" => "Tamaris"],
                                                ["arabic" => "تملالت", "eng" => "Tamellalt"],
                                                ["arabic" => "تامصلوحت", "eng" => "Tameslohte"],
                                                ["arabic" => "تامسنا", "eng" => "Tamesna"],
                                                ["arabic" => "تمراغت", "eng" => "Tamraght"],
                                                ["arabic" => "تمسمان", "eng" => "Tamsamane"],
                                                ["arabic" => "تامزموت", "eng" => "Tamzmout"],
                                                ["arabic" => "تانديت", "eng" => "Tandit"],
                                                ["arabic" => "طنجة", "eng" => "Tanger"],
                                                ["arabic" => "تنوغة", "eng" => "Tanougha"],
                                                ["arabic" => "طانطان", "eng" => "TANTAN"],
                                                ["arabic" => "تاونات", "eng" => "Taounate"],
                                                ["arabic" => "تاوريرت", "eng" => "Taourirt"],
                                                ["arabic" => "طرفاية", "eng" => "Tarfaya"],
                                                ["arabic" => "تاركيست", "eng" => "Targuist"],
                                                ["arabic" => "تارودانت", "eng" => "Taroudant"],
                                                ["arabic" => "تراست", "eng" => "Tarrast"],
                                                ["arabic" => "تاسلطانت", "eng" => "Tassoultante"],
                                                ["arabic" => "طاطا", "eng" => "TATA"],
                                                ["arabic" => "تازة", "eng" => "TAZA"],
                                                ["arabic" => "تازارين", "eng" => "Tazarine"],
                                                ["arabic" => "تازيناخت", "eng" => "Tazenakht"],
                                                ["arabic" => "تازناخت", "eng" => "Taznakht"],
                                                ["arabic" => "ثلاث أزلاف", "eng" => "Telat Azlaf"],
                                                ["arabic" => "تمارة", "eng" => "Temara"],
                                                ["arabic" => "تمسية", "eng" => "Temsia"],
                                                ["arabic" => "تندارة", "eng" => "Tendrara"],
                                                ["arabic" => "تروال - وزان", "eng" => "Teroual - Ouazzane"],
                                                ["arabic" => "تطوان", "eng" => "Tetouan"],
                                                ["arabic" => "ظهر السوق", "eng" => "Thar Es-Souk"],
                                                ["arabic" => "تيداس", "eng" => "Tiddas"],
                                                ["arabic" => "تيدزي", "eng" => "Tidzi"],
                                                ["arabic" => "تيفلت", "eng" => "TIFLET"],
                                                ["arabic" => "تيغسالين", "eng" => "Tighssaline"],
                                                ["arabic" => "تيكوين", "eng" => "Tikiouine"],
                                                ["arabic" => "تمحضيت", "eng" => "Timahdite"],
                                                ["arabic" => "تيموليلت", "eng" => "Timoulilte"],
                                                ["arabic" => "تين منصور", "eng" => "Tin Mansour"],
                                                ["arabic" => "تنجداد", "eng" => "Tinejdad"],
                                                ["arabic" => "تنغير", "eng" => "Tinghir"],
                                                ["arabic" => "تيسة", "eng" => "Tissa"],
                                                ["arabic" => "تسينت", "eng" => "Tissint"],
                                                ["arabic" => "تيت مليل", "eng" => "Tit Mellil"],
                                                ["arabic" => "تيزي وسلي", "eng" => "Tizi Ouasli"],
                                                ["arabic" => "تيزنيت", "eng" => "Tiznit"],
                                                ["arabic" => "تيزتوتين", "eng" => "Tiztoutine"],
                                                ["arabic" => "اثنين شتوكة الجديدة", "eng" => "Tnine Chtouka El jadida"],
                                                ["arabic" => "اثنين الغربية", "eng" => "Tnine Gharbia"],
                                                ["arabic" => "اليوسفية", "eng" => "Youssoufia"],
                                                ["arabic" => "الزاك", "eng" => "Zag"],
                                                ["arabic" => "زاكورة", "eng" => "Zagora"],
                                                ["arabic" => "زايدة", "eng" => "Zaida"],
                                                ["arabic" => "زايو", "eng" => "Zaio"],
                                                ["arabic" => "زاوية بوكرين - صفرو", "eng" => "Zaouiat Bougrine - Sefrou"],
                                                ["arabic" => "زاوية الشيخ", "eng" => "Zaouiat Cheikh"],
                                                ["arabic" => "زاوية سيدي إسماعيل", "eng" => "Zaouit Sidi Smail"],
                                                ["arabic" => "زغنغن", "eng" => "Zeghanghane"],
                                                ["arabic" => "زناتة", "eng" => "Zenata"],
                                                ["arabic" => "زومي", "eng" => "Zoumi"],
                                                ["arabic" => "زريزر", "eng" => "Zrizer"]
                                            ];
                                        @endphp
                                        @foreach($cities as $city)
                                            <option value="{{ $city['eng'] }}">{{ $city['eng'] }} - {{ $city['arabic'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
                    <div class="card-body p-4 p-md-5 text-center">
                        <i class="fas fa-wallet fa-3x text-dark mb-3 opacity-25"></i>
                        <h4 class="fw-black mb-3 h5 text-uppercase ls-1">{{ __('Payment Method') }}</h4>
                        <div class="p-3 border rounded-4 bg-light d-inline-block px-5">
                            <span class="fw-bold h6 m-0"><i class="fas fa-money-bill-wave me-2"></i> {{ __('Cash on Delivery') }}</span>
                        </div>
                        <p class="text-muted small mt-3">{{ __('COD Description') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 100px;">
                    <div class="card-header bg-white p-4 border-bottom-0 pb-0">
                        <h5 class="fw-black m-0 text-uppercase ls-1" style="font-size: 1rem;">{{ __('Order Summary') }}</h5>
                    </div>
                    <div class="card-body p-4 pt-2">
                        @foreach($cart as $key => $details)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3 position-relative">
                                @if($details['image'])
                                    <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" 
                                         class="rounded-3 border aspect-9-10" style="width: 60px;">
                                @else
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center aspect-9-10" style="width: 60px;">
                                        <i class="fas fa-image text-muted opacity-25 fa-2x"></i>
                                    </div>
                                @endif
                                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-dark border-0 shadow-sm">{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-truncate" style="max-width: 160px; font-size: 0.9rem;">{{ $details['name'] }}</h6>
                                <div class="small text-muted" style="font-size: 0.75rem;">
                                    @if(($details['color'] ?? null)) {{ $details['color'] }} @endif
                                    @if(($details['color'] ?? null) && ($details['size'] ?? null)) | @endif
                                    @if(($details['size'] ?? null)) {{ $details['size'] }} @endif
                                </div>
                            </div>
                            <div class="fw-bold small text-dark">{{ currency($details['price'] * $details['quantity']) }}</div>
                        </div>
                        @endforeach
                        
                        <div class="bg-light p-3 rounded-3 mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">{{ __('Sub-total') }}</span>
                                <span class="fw-bold small">{{ currency($total) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted small">{{ __('Delivery') }}</span>
                                <span class="text-success fw-bold small">{{ __('Free') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <span class="fw-black mb-0 text-uppercase ls-1">{{ __('TOTAL') }}</span>
                                <span class="h4 fw-black mb-0" style="color: var(--accent);">{{ currency($total) }}</span>
                            </div>
                        </div>

                        <button type="submit" form="checkout-form" class="btn btn-dark btn-lg w-100 rounded-pill py-3 fw-black shadow mt-4 text-uppercase ls-1" style="font-size: 0.95rem;">
                            {{ __('Confirm Order') }} <i class="fas fa-check-circle ms-2"></i>
                        </button>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('cart.index') }}" class="text-muted text-decoration-none small fw-bold">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('Modify Cart') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Tom Select for Cities
        new TomSelect('[name="shipping_city"]', {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "{{ __('Select Your City') }}",
            allowEmptyOption: true,
            maxOptions: 500,
            dropdownParent: 'body',
            render: {
                no_results: function(data, escape) {
                    return '<div class="no-results">' + escape("{{ __('No cities found matching your search') }}") + '</div>';
                }
            }
        });

        if (typeof trackAdEvent === 'function') {
            trackAdEvent('InitiateCheckout', {
                content_ids: [ @foreach($cart as $key => $details) '{{ explode('_', $key)[0] }}', @endforeach ],
                content_type: 'product',
                value: {{ $total }},
                currency: 'MAD',
                num_items: {{ count($cart) }}
            });
        }
    });
</script>
@endpush
