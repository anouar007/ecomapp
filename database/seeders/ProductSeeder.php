<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::updateOrCreate(
            ['slug' => 'miel-produits-naturels'],
            [
                'name' => 'Miel & Produits Naturels',
                'name_en' => 'Honey & Natural Products',
                'name_fr' => 'Miel & Produits Naturels',
                'name_ar' => 'العسل والمنتجات الطبيعية',
                'description' => 'Premium natural Moroccan treasures directly from our cooperative.',
                'status' => 'active',
            ]
        );

        // Remove old products to avoid duplicates
        Product::where('category_id', $category->id)->delete();

        $products = [
            [
                'name_ar' => 'عسل السدر الجبلي',
                'name_en' => 'Premium Mountain Sidr Honey',
                'name_fr' => 'Miel de Sidr (Jujubier) de Montagne',
                'desc_ar' => "عسل السدر الجبلي الأصيل\nعسل طبيعي فاخر مستخلص من رحيق أزهار أشجار السدر في قلب جبال الأطلس. يتميز بلونه الذهبي الداكن، قوامه الكثيف ورائحته العطرية القوية. معروف بحلاوته المتوازنة وفوائده الصحية الاستثنائية.\n\nالمميزات الرئيسية:\n🌼 طبيعي 100٪ بدون إضافات\n🍯 طعم غني وفريد من نوعه\n🌿 غني بمضادات الأكسدة والمعادن الطبيعية\n😌 يقوي المناعة ويحسن الطاقة العامة\n🍵 مثالي للتحلية الصحية والعلاجات الطبيعية\n\nطريقة الاستخدام:\nاستمتع به بمفرده، أو تناول ملعقة على الريق، أو أضفه إلى المشروبات الساخنة والوصفات الصحية.\nالتخزين:\nيحفظ في مكان بارد وجاف بعيداً عن ضوء الشمس المباشر.\nالوزن: حسب الاختيار\nالمنشأ: المغرب 🇲🇦\n✨ خيار متميز لمحبي العسل الصافي والأصيل.",
                'desc_en' => "Premium Mountain Sidr Honey\nA premium natural honey harvested from the nectar of Sidr trees in the heart of the Atlas Mountains. It features a dark golden color, a thick texture, and a strong aromatic scent. Known for its balanced sweetness and exceptional health benefits.\n\nKey Benefits:\n🌼 100% natural with no additives\n🍯 Rich and unique taste\n🌿 High in antioxidants and natural minerals\n😌 Boosts immunity and overall energy\n🍵 Perfect for healthy sweetening and natural remedies\n\nHow to Use:\nEnjoy it on its own, take a spoonful on an empty stomach, or add it to hot drinks and healthy recipes.\nStorage:\nStore in a cool, dry place away from direct sunlight.\nWeight: Depending on selection\nOrigin: Morocco 🇲🇦\n✨ A refined choice for lovers of pure, authentic honey.",
                'desc_fr' => "Miel de Sidr de Montagne\nUn miel naturel de qualité supérieure récolté sur les jujubiers au cœur des montagnes de l'Atlas. Il présente une couleur dorée foncée, une texture épaisse et un parfum aromatique puissant. Connu pour sa douceur équilibrée et ses bienfaits exceptionnels pour la santé.\n\nBienfaits Clés :\n🌼 100% naturel sans additifs\n🍯 Goût riche et unique\n🌿 Riche en antioxydants et minéraux naturels\n😌 Renforce l'immunité et l'énergie globale\n🍵 Parfait pour sucrer sainement et pour les remèdes naturels\n\nConseils d'utilisation :\nÀ déguster pur, une cuillère à jeun, ou à ajouter à vos boissons chaudes et recettes saines.\nConservation :\nConserver dans un endroit frais et sec, à l'abri de la lumière.\nPoids : Selon la sélection\nOrigine : Maroc 🇲🇦\n✨ Un choix raffiné pour les amateurs de miel pur et authentique.",
                'variants' => [
                    ['size' => '1KG', 'price' => 200],
                    ['size' => '500G', 'price' => 100],
                    ['size' => '250G', 'price' => 50],
                ]
            ],
            [
                'name_ar' => 'عسل الدغموس',
                'name_en' => 'Organic Daghmous Honey',
                'name_fr' => 'Miel de Daghmous (Euphorbe)',
                'desc_ar' => "عسل الدغموس المغربي الحر\nعسل نادر وقوي مستخلص من نبات الدغموس الصحراوي والجبلي. يتميز بمذاقه الحار الذي يترك شعوراً بالدفء في الحلق. يعتبر من أقوى أنواع العسل العلاجية في المغرب.\n\nالمميزات الرئيسية:\n🌼 حر وطبيعي 100٪\n🍯 طعم قوي وحار ومميز\n🌿 فعال جداً ضد أمراض الجهاز التنفسي\n😌 يقوي جهاز المناعة بشكل طبيعي\n🍵 مضاد حيوي طبيعي فعال\n\nطريقة الاستخدام:\nيفضل تناوله مع الماء الدافئ أو الحليب، أو ملعقة صغيرة يومياً للوقاية.\nالتخزين:\nيحفظ في مكان بارد وجاف بعيداً عن الضوء.\nالوزن: حسب الاختيار\nالمنشأ: المغرب 🇲🇦\n✨ كنز طبيعي للصحة والمناعة.",
                'desc_en' => "Organic Daghmous Honey\nA rare and potent honey harvested from the Daghmous (Euphorbia) plant. It features a unique spicy taste that leaves a warming sensation in the throat. Known as one of the most powerful therapeutic honeys in Morocco.\n\nKey Benefits:\n🌼 100% pure and natural\n🍯 Strong, spicy, and distinctive flavor\n🌿 Very effective for respiratory health\n😌 Naturally boosts the immune system\n🍵 Powerful natural antibiotic properties\n\nHow to Use:\nBest enjoyed with warm water or milk, or a small spoonful daily for prevention.\nStorage:\nStore in a cool, dry place away from light.\nWeight: Depending on selection\nOrigin: Morocco 🇲🇦\n✨ A natural treasure for health and immunity.",
                'desc_fr' => "Miel de Daghmous (Euphorbe)\nUn miel rare et puissant récolté sur la plante d'Euphorbe (Daghmous). Il se distingue par un goût épicé unique qui laisse une sensation de chaleur dans la gorge. Reconnu comme l'un des miels thérapeutiques les plus puissants du Maroc.\n\nBienfaits Clés :\n🌼 100% pur et naturel\n🍯 Saveur forte, épicée et distinctive\n🌿 Très efficace pour la santé respiratoire\n😌 Renforce naturellement le système immunitaire\n🍵 Propriétés antibiotiques naturelles puissantes\n\nConseils d'utilisation :\nÀ déguster de préférence avec de l'eau tiède ou du lait, ou une petite cuillère par jour en prévention.\nConservation :\nConserver dans un endroit frais et sec, à l'abri de la lumière.\nPoids : Selon la sélection\nOrigine : Maroc 🇲🇦\n✨ Un trésor naturel pour la santé et l'immunité.",
                'variants' => [
                    ['size' => '1KG', 'price' => 350],
                    ['size' => '500G', 'price' => 175],
                    ['size' => '250G', 'price' => 90],
                ]
            ],
            [
                'name_ar' => 'عسل الزعتر',
                'name_en' => 'Wild Thyme Honey',
                'name_fr' => 'Miel de Thym Sauvage',
                'desc_ar' => "عسل الزعتر البري\nعسل عطري فاخر مستخلص من زهور الزعتر البري في جبال الأطلس. يتميز بلونه العنبري وقوامه المتماسك ورائحته النفاذة التي تذكرنا بجبال المغرب العريقة.\n\nالمميزات الرئيسية:\n🌼 طبيعي 100٪ بدون إضافات\n🍯 رائحة عطرية قوية وطعم لذيذ\n🌿 خصائص مطهرة ومضادة للالتهابات\n😌 مهدئ للسعال ومفيد للهضم\n🍵 مثالي لتحلية المشروبات العشبية\n\nطريقة الاستخدام:\nرائع مع الشاي، أو فوق الخبز المحمص، أو كجزء من روتينك الصحي الصباحي.\nالتخزين:\nيحفظ في مكان جاف وبارد.\nالوزن: حسب الاختيار\nالمنشأ: المغرب 🇲🇦\n✨ عطر الجبال في كل ملعقة.",
                'desc_en' => "Wild Thyme Honey\nA premium aromatic honey harvested from wild thyme flowers in the Atlas Mountains. It features an amber color, firm texture, and a pungent aroma that reminds us of ancient Moroccan mountains.\n\nKey Benefits:\n🌼 100% natural with no additives\n🍯 Strong aromatic scent and delicious taste\n🌿 Antiseptic and anti-inflammatory properties\n😌 Calms coughs and supports digestion\n🍵 Perfect for sweetening herbal drinks\n\nHow to Use:\nGreat with tea, on toast, or as part of your morning health routine.\nStorage:\nStore in a cool, dry place.\nWeight: Depending on selection\nOrigin: Morocco 🇲🇦\n✨ The scent of the mountains in every spoonful.",
                'desc_fr' => "Miel de Thym Sauvage\nUn miel aromatique d'exception récolté sur les fleurs de thym sauvage des montagnes de l'Atlas. Il présente une couleur ambrée, une texture ferme et un parfum pénétrant qui rappelle les montagnes marocaines.\n\nBienfaits Clés :\n🌼 100% naturel sans additifs\n🍯 Parfum aromatique puissant et goût délicieux\n🌿 Propriétés antiseptiques et anti-inflammatoires\n😌 Calme la toux et facilite la digestion\n🍵 Idéal pour sucrer les infusions\n\nConseils d'utilisation :\nExcellent avec le thé, sur des tartines, ou dans votre routine santé du matin.\nConservation :\nConserver dans un endroit sec et frais.\nPoids : Selon la sélection\nOrigine : Maroc 🇲🇦\n✨ Le parfum des montagnes dans chaque cuillère.",
                'variants' => [
                    ['size' => '1KG', 'price' => 360],
                    ['size' => '500G', 'price' => 180],
                    ['size' => '250G', 'price' => 95],
                ]
            ],
            [
                'name_ar' => 'عسل الزكوم',
                'name_en' => 'Rare Zakkoum Honey',
                'name_fr' => 'Miel de Zakkoum',
                'desc_ar' => "عسل الزكوم النادر\nعسل متميز مستخلص من أزهار نبتة الزكوم (الصبّار الجبلي). يتميز بلونه الفاتح ومذاقه الذي يجمع بين الحلاوة والحدة الخفيفة. يعتبر من الكنوز الطبيعية للأطلس.\n\nالمميزات الرئيسية:\n🌼 طبيعي 100٪ ونقي\n🍯 قوام حريري وطعم فريد\n🌿 مفيد جداً لأمراض القلب والشرايين\n😌 يساعد في تنظيم الضغط وتحسين الدورة الدموية\n🍵 مغذٍ ومنشط طبيعي\n\nطريقة الاستخدام:\nملعقة كبيرة يومياً على الريق، أو يمزج مع الماء الدافئ.\nالتخزين:\nيحفظ في مكان بارد بعيداً عن الرطوبة.\nالوزن: حسب الاختيار\nالمنشأ: المغرب 🇲🇦\n✨ حليف طبيعي لصحة قلبك.",
                'desc_en' => "Rare Zakkoum Honey\nA distinctive honey harvested from the flowers of the Zakkoum plant (mountain cactus). It features a light color and a taste that combines sweetness with a slight sharpness. Considered one of the natural treasures of the Atlas.\n\nKey Benefits:\n🌼 100% natural and pure\n🍯 Silky texture and unique taste\n🌿 Highly beneficial for cardiovascular health\n😌 Helps regulate pressure and improve circulation\n🍵 Nutritious and a natural stimulant\n\nHow to Use:\nA tablespoon daily on an empty stomach, or mix with warm water.\nStorage:\nStore in a cool place away from moisture.\nWeight: Depending on selection\nOrigin: Morocco 🇲🇦\n✨ A natural ally for your heart health.",
                'desc_fr' => "Miel de Zakkoum\nUn miel distinctif récolté sur les fleurs de la plante de Zakkoum (cactus de montagne). Il présente une couleur claire et un goût qui allie douceur et légère pointe d'amertume. Considéré comme l'un des trésors naturels de l'Atlas.\n\nBienfaits Clés :\n🌼 100% naturel et pur\n🍯 Texture soyeuse et goût unique\n🌿 Très bénéfique pour la santé cardiovasculaire\n😌 Aide à réguler la tension et améliore la circulation\n🍵 Nutritif et stimulant naturel\n\nConseils d'utilisation :\nUne cuillère à soupe par jour à jeun, ou mélangé à de l'eau tiède.\nConservation :\nConserver dans un endroit frais à l'abri de l'humidité.\nPoids : Selon la sélection\nOrigine : Maroc 🇲🇦\n✨ Un allié naturel pour votre santé cardiaque.",
                'variants' => [
                    ['size' => '1KG', 'price' => 200],
                    ['size' => '500G', 'price' => 100],
                    ['size' => '250G', 'price' => 55],
                ]
            ],
            [
                'name_ar' => 'عسل الفرنان',
                'name_en' => 'Artisanal Fernan Honey',
                'name_fr' => 'Miel de Fernan Artisanal',
                'desc_ar' => "عسل الفرنان الطبيعي\nعسل غابوي أصيل مستخلص من أشجار الفرنان. يتميز بلونه الداكن وقوامه الكثيف ومذاقه القوي الغني بالمعادن. خيار ممتاز لمن يبحث عن الفوائد الصحية المركزة.\n\nالمميزات الرئيسية:\n🌼 مستخلص طبيعياً وبدون تصفية حرارية\n🍯 طعم قوي وغني بالمعادن\n🌿 يعالج مشاكل فقر الدم والضعف العام\n😌 منشط قوي للجسم والذاكرة\n🍵 بديل صحي ممتاز للسكر\n\nطريقة الاستخدام:\nيمكن استخدامه في الحلويات الطبيعية، أو كجزء من وجبة الفطور الصحية.\nالتخزين:\nيحفظ في مكان جاف وبارد.\nالوزن: حسب الاختيار\nالمنشأ: المغرب 🇲🇦\n✨ طاقة الغابة في كل ملعقة.",
                'desc_en' => "Artisanal Fernan Honey\nAn authentic forest honey harvested from Fernan trees. It features a dark color, thick texture, and a strong taste rich in minerals. An excellent choice for those seeking concentrated health benefits.\n\nKey Benefits:\n🌼 Naturally extracted without heat filtration\n🍯 Strong and mineral-rich taste\n🌿 Treats anemia and general weakness issues\n😌 Powerful stimulant for the body and memory\n🍵 Excellent healthy sugar substitute\n\nHow to Use:\nCan be used in natural desserts or as part of a healthy breakfast.\nStorage:\nStore in a dry, cool place.\nWeight: Depending on selection\nOrigin: Morocco 🇲🇦\n✨ Forest energy in every spoonful.",
                'desc_fr' => "Miel de Fernan Artisanal\nUn miel de forêt authentique récolté sur les arbres de Fernan. Il présente une couleur sombre, une texture épaisse et un goût prononcé riche en minéraux. Un excellent choix pour ceux qui recherchent des bienfaits concentrés.\n\nBienfaits Clés :\n🌼 Extrait naturellement sans filtration thermique\n🍯 Goût fort et riche en minéraux\n🌿 Traite les problèmes d'anémie et de faiblesse générale\n😌 Puissant stimulant pour le corps et la mémoire\n🍵 Excellent substitut sain au sucre\n\nConseils d'utilisation :\nPeut être utilisé dans les desserts naturels ou dans un petit-déjeuner sain.\nConservation :\nConserver dans un endroit sec et frais.\nPoids : Selon la sélection\nOrigine : Maroc 🇲🇦\n✨ L'énergie de la forêt dans chaque cuillère.",
                'variants' => [
                    ['size' => '1KG', 'price' => 200],
                    ['size' => '500G', 'price' => 100],
                    ['size' => '250G', 'price' => 55],
                ]
            ],
            [
                'name_ar' => 'عسل الخروب',
                'name_en' => 'Pure Carob Honey',
                'name_fr' => 'Miel de Caroubier Pur',
                'desc_ar' => "عسل الخروب الصحي\nعسل طبيعي متميز مستخلص من أزهار شجر الخروب. يتميز بلونه البني الداكن وقوامه الكريمي ومذاقه الذي يشبه الشوكولاتة والكراميل بشكل طبيعي.\n\nالمميزات الرئيسية:\n🌼 طبيعي 100٪ وغني بالألياف\n🍯 مذاق فريد يشبه الكراميل\n🌿 مفيد جداً لمشاكل الجهاز الهضمي والقولون\n😌 مهدئ طبيعي ومنشط للأمعاء\n🍵 رائع للأطفال والرياضيين\n\nطريقة الاستخدام:\nاستمتع به مع الفطائر، أو أضفه إلى الزبادي، أو ملعقة يومياً لتحسين الهضم.\nالتخزين:\nيحفظ في درجة حرارة الغرفة بعيداً عن الرطوبة.\nالوزن: حسب الاختيار\nالمنشأ: المغرب 🇲🇦\n✨ حلاوة صحية وهضم مثالي.",
                'desc_en' => "Pure Carob Honey\nA distinctive natural honey harvested from carob tree flowers. It features a dark brown color, creamy texture, and a taste that naturally resembles chocolate and caramel.\n\nKey Benefits:\n🌼 100% natural and rich in fiber\n🍯 Unique caramel-like taste\n🌿 Highly beneficial for digestive and colon issues\n😌 Natural sedative and intestinal stimulant\n🍵 Great for children and athletes\n\nHow to Use:\nEnjoy it with pancakes, add to yogurt, or a spoonful daily to improve digestion.\nStorage:\nStore at room temperature away from moisture.\nWeight: Depending on selection\nOrigin: Morocco 🇲🇦\n✨ Healthy sweetness and perfect digestion.",
                'desc_fr' => "Miel de Caroubier Pur\nUn miel naturel distinctif récolté sur les fleurs de caroubier. Il présente une couleur brun foncé, une texture crémeuse et un goût qui rappelle naturellement le chocolat et le caramel.\n\nBienfaits Clés :\n🌼 100% naturel et riche en fibres\n🍯 Goût unique rappelant le caramel\n🌿 Très bénéfique pour les problèmes digestifs et intestinaux\n😌 Apaisant naturel et stimulant intestinal\n🍵 Idéal pour les enfants et les sportifs\n\nConseils d'utilisation :\nÀ déguster avec des crêpes, à ajouter au yaourt, ou une cuillère par jour pour améliorer la digestion.\nConservation :\nConserver à température ambiante à l'abri de l'humidité.\nPoids : Selon la sélection\nOrigine : Maroc 🇲🇦\n✨ Douceur saine et digestion parfaite.",
                'variants' => [
                    ['size' => '1KG', 'price' => 230],
                    ['size' => '500G', 'price' => 115],
                    ['size' => '250G', 'price' => 65],
                ]
            ],
            [
                'name_ar' => 'عسل الأعشاب',
                'name_en' => 'Wild Flower Herbal Honey',
                'name_fr' => 'Miel de Fleurs Sauvages',
                'desc_ar' => "عسل الأعشاب البرية\nعسل طبيعي متنوع مستخلص من رحيق مئات الزهور البرية والأعشاب الجبلية. يتميز بتنوع فوائده وتوازن مذاقه ولونه الذهبي الجذاب.\n\nالمميزات الرئيسية:\n🌼 طبيعي 100٪ بدون إضافات\n🍯 طعم متوازن ومحبب للجميع\n🌿 غني بمجموعة واسعة من حبوب اللقاح والإنزيمات\n😌 مقوٍ عام للجسم وفاتح للشهية\n🍵 مناسب جداً للاستخدام اليومي والحلويات\n\nطريقة الاستخدام:\nمثالي للتحلية اليومية، أو في وجبات الإفطار، أو كقناع طبيعي للبشرة.\nالتخزين:\nيحفظ في مكان بارد وجاف.\nالوزن: حسب الاختيار\nالمنشأ: المغرب 🇲🇦\n✨ جوهر الطبيعة المتنوعة في وعاء واحد.",
                'desc_en' => "Wild Flower Herbal Honey\nA diverse natural honey harvested from the nectar of hundreds of wild flowers and mountain herbs. It features a balance of benefits, a balanced taste, and an attractive golden color.\n\nKey Benefits:\n🌼 100% natural with no additives\n🍯 Balanced and pleasant taste for everyone\n🌿 Rich in a wide range of pollens and enzymes\n😌 General body tonic and appetite stimulant\n🍵 Highly suitable for daily use and desserts\n\nHow to Use:\nPerfect for daily sweetening, breakfast, or as a natural skin mask.\nStorage:\nStore in a cool, dry place.\nWeight: Depending on selection\nOrigin: Morocco 🇲🇦\n✨ The essence of diverse nature in one jar.",
                'desc_fr' => "Miel de Fleurs Sauvages\nUn miel naturel polyvalent récolté sur le nectar de centaines de fleurs sauvages et d'herbes de montagne. Il présente un équilibre de bienfaits, un goût équilibré et une couleur dorée attrayante.\n\nBienfaits Clés :\n🌼 100% naturel sans additifs\n🍯 Goût équilibré et agréable pour tous\n🌿 Riche en une large gamme de pollens et d'enzymes\n😌 Tonique général et stimulant de l'appétit\n🍵 Très adapté à un usage quotidien et aux desserts\n\nConseils d'utilisation :\nParfait pour sucrer au quotidien, au petit-déjeuner, ou comme masque naturel pour la peau.\nConservation :\nConserver dans un endroit frais et sec.\nPoids : Selon la sélection\nOrigine : Maroc 🇲🇦\n✨ L'essence de la nature diversifiée dans un seul pot.",
                'variants' => [
                    ['size' => '1KG', 'price' => 180],
                    ['size' => '500G', 'price' => 90],
                    ['size' => '250G', 'price' => 50],
                ]
            ],
            [
                'name_ar' => 'عسل الكالتوس',
                'name_en' => 'Refreshing Eucalyptus Honey',
                'name_fr' => 'Miel d\'Eucalyptus',
                'desc_ar' => "عسل الكالتوس المنعش\nعسل طبيعي متميز برائحته العطرية القوية التي تذكرنا بغابات الأوكالبتوس. يتميز بمذاقه المنعش وقوامه السائل الذي يميل إلى التبلور الناعم.\n\nالمميزات الرئيسية:\n🌼 طبيعي 100٪ ونقي\n🍯 طعم منعش ورائحة عطرية\n🌿 فعال جداً لنزلات البرد والسعال\n😌 يساعد على فتح المجاري التنفسية\n🍵 مطهر طبيعي للمسالك البولية\n\nطريقة الاستخدام:\nأضفه إلى الشاي الدافئ مع الليمون للتخفيف من أعراض الزكام.\nالتخزين:\nيحفظ في مكان جاف بعيداً عن الحرارة.\nالوزن: حسب الاختيار\nالمنشأ: المغرب 🇲🇦\n✨ انتعاش طبيعي وصحة دائمة.",
                'desc_en' => "Refreshing Eucalyptus Honey\nA natural honey distinguished by its strong aromatic scent that reminds us of eucalyptus forests. It features a refreshing taste and a liquid texture that tends toward soft crystallization.\n\nKey Benefits:\n🌼 100% natural and pure\n🍯 Refreshing taste and aromatic scent\n🌿 Very effective for colds and coughs\n😌 Helps open the respiratory tract\n🍵 Natural urinary tract antiseptic\n\nHow to Use:\nAdd to warm tea with lemon to alleviate cold symptoms.\nStorage:\nStore in a dry place away from heat.\nWeight: Depending on selection\nOrigin: Morocco 🇲🇦\n✨ Natural refreshment and lasting health.",
                'desc_fr' => "Miel d'Eucalyptus\nUn miel naturel distingué par son fort parfum aromatique qui rappelle les forêts d'eucalyptus. Il présente un goût rafraîchissant et une texture liquide qui tend vers une cristallisation douce.\n\nBienfaits Clés :\n🌼 100% naturel et pur\n🍯 Goût rafraîchissant et parfum aromatique\n🌿 Très efficace contre les rhumes et la toux\n😌 Aide à dégager les voies respiratoires\n🍵 Antiseptique naturel des voies urinaires\n\nConseils d'utilisation :\nÀ ajouter au thé chaud avec du citron pour soulager les symptômes du rhume.\nConservation :\nConserver dans un endroit sec à l'abri de la chaleur.\nPoids : Selon la sélection\nOrigine : Maroc 🇲🇦\n✨ Fraîcheur naturelle et santé durable.",
                'variants' => [
                    ['size' => '1KG', 'price' => 210],
                    ['size' => '500G', 'price' => 105],
                    ['size' => '250G', 'price' => 60],
                ]
            ],
            [
                'name_ar' => 'عسل الليمون',
                'name_en' => 'Citrus Lemon Honey',
                'name_fr' => 'Miel de Citronnier',
                'desc_ar' => "عسل الليمون العطري\nعسل خفيف ومنعش مستخلص من رحيق أزهار الليمون والبرتقال. يتميز بلونه الذهبي الفاتح وقوامه الناعم ورائحته الحمضية اللطيفة.\n\nالمميزات الرئيسية:\n🌼 طبيعي 100٪ بدون إضافات\n🍯 حلاوة خفيفة وطعم منعش\n🌿 غني بفيتامين C ومضادات الأكسدة\n😌 مهدئ للأعصاب ويساعد على الاسترخاء\n🍵 مثالي للأطفال ولتحلية العصائر\n\nطريقة الاستخدام:\nرائع للتحلية اليومية، أو كجزء من تتبيلات السلطة الصحية.\nالتخزين:\nيحفظ في مكان بارد وجاف.\nالوزن: حسب الاختيار\nالمنشأ: المغرب 🇲🇦\n✨ لمسة من الانتعاش في كل ملعقة.",
                'desc_en' => "Citrus Lemon Honey\nA light and refreshing honey harvested from the nectar of lemon and orange blossoms. It features a light golden color, smooth texture, and a pleasant citrus aroma.\n\nKey Benefits:\n🌼 100% natural with no additives\n🍯 Light sweetness and refreshing taste\n🌿 Rich in vitamin C and antioxidants\n😌 Calms nerves and promotes relaxation\n🍵 Perfect for children and sweetening juices\n\nHow to Use:\nGreat for daily sweetening or as part of healthy salad dressings.\nStorage:\nStore in a cool, dry place.\nWeight: Depending on selection\nOrigin: Morocco 🇲🇦\n✨ A touch of refreshment in every spoonful.",
                'desc_fr' => "Miel de Citronnier\nUn miel léger et rafraîchissant récolté sur le nectar des fleurs de citronnier et d'oranger. Il présente une couleur dorée claire, une texture lisse et un agréable parfum d'agrumes.\n\nBienfaits Clés :\n🌼 100% naturel sans additifs\n🍯 Douceur légère et goût rafraîchissant\n🌿 Riche en vitamine C et antioxydants\n😌 Calme les nerfs et favorise la relaxation\n🍵 Idéal pour les enfants et pour sucrer les jus\n\nConseils d'utilisation :\nExcellent pour sucrer au quotidien ou dans des vinaigrettes saines.\nConservation :\nConserver dans un endroit frais et sec.\nPoids : Selon la sélection\nOrigine : Maroc 🇲🇦\n✨ Une touche de fraîcheur dans chaque cuillère.",
                'variants' => [
                    ['size' => '1KG', 'price' => 110],
                    ['size' => '500G', 'price' => 55],
                    ['size' => '250G', 'price' => 30],
                ]
            ],
            [
                'name_ar' => 'أملو المغربي الأصيل',
                'name_en' => 'Authentic Moroccan Amlou',
                'name_fr' => 'Amlou Marocain Authentique',
                'desc_ar' => "أملو المغربي الأصيل\nمزيج تقليدي فاخر يجمع بين أجود أنواع اللوز المحمص، زيت الأركان النقي، وعسل النحل الطبيعي. يعتبر وجبة متكاملة تمنح الجسم طاقة طبيعية ومذاقاً لا يقاوم.\n\nالمميزات الرئيسية:\n🌼 طبيعي 100٪ بدون سكر مضاف أو مواد حافظة\n🍯 قوام كريمي غني بنكهة اللوز والأركان\n🌿 مصدر ممتاز للأحماض الدهنية الصحية وفيتامين E\n😌 منشط طبيعي للجسم والذهن\n🍵 رائع لوجبة الإفطار أو كوجبة خفيفة مغذية\n\nطريقة الاستخدام:\nيُحرك جيداً قبل الاستخدام، ويُقدم مع الخبز المغربي أو يُضاف إلى العصائر الصحية.\nالتخزين:\nيحفظ في مكان جاف في درجة حرارة الغرفة. لا يحتاج إلى تبريد.\nالوزن: حسب الاختيار\nالمنشأ: المغرب 🇲🇦\n✨ طاقة الطبيعة في مزيج واحد.",
                'desc_en' => "Authentic Moroccan Amlou\nA luxurious traditional blend combining the finest roasted almonds, pure Argan oil, and natural honey. Considered a complete meal that gives the body natural energy and an irresistible taste.\n\nKey Benefits:\n🌼 100% natural with no added sugar or preservatives\n🍯 Rich creamy texture with almond and Argan flavor\n🌿 Excellent source of healthy fatty acids and vitamin E\n😌 Natural stimulant for body and mind\n🍵 Great for breakfast or as a nutritious snack\n\nHow to Use:\nStir well before use, serve with Moroccan bread or add to healthy smoothies.\nStorage:\nStore in a dry place at room temperature. No refrigeration needed.\nWeight: Depending on selection\nOrigin: Morocco 🇲🇦\n✨ Nature's energy in one blend.",
                'desc_fr' => "Amlou Marocain Authentique\nUn mélange traditionnel luxueux combinant les meilleures amandes grillées, de l'huile d'Argan pure et du miel naturel. Considéré comme un repas complet qui donne au corps une énergie naturelle et un goût irrésistible.\n\nBienfaits Clés :\n🌼 100% naturel sans sucre ajouté ni conservateurs\n🍯 Texture crémeuse riche au goût d'amande et d'Argan\n🌿 Excellente source d'acides gras sains et de vitamine E\n😌 Stimulant naturel pour le corps et l'esprit\n🍵 Idéal pour le petit-déjeuner ou comme collation nutritive\n\nConseils d'utilisation :\nBien mélanger avant usage, servir avec du pain marocain ou ajouter aux smoothies sains.\nConservation :\nConserver dans un endroit sec à température ambiante. Pas de réfrigération nécessaire.\nPoids : Selon la sélection\nOrigine : Maroc 🇲🇦\n✨ L'énergie de la nature dans un seul mélange.",
                'variants' => [
                    ['size' => '800G', 'price' => 200],
                    ['size' => '400G', 'price' => 100],
                ]
            ],
        ];

        foreach ($products as $pData) {
            $product = Product::create([
                'category_id' => $category->id,
                'name' => $pData['name_en'],
                'name_en' => $pData['name_en'],
                'name_fr' => $pData['name_fr'],
                'name_ar' => $pData['name_ar'],
                'description' => $pData['desc_en'],
                'description_en' => $pData['desc_en'],
                'description_fr' => $pData['desc_fr'],
                'description_ar' => $pData['desc_ar'],
                'price' => $pData['variants'][0]['price'],
                'stock' => 100,
                'status' => 'active',
                'sku' => 'HON-' . strtoupper(Str::random(6)),
            ]);

            foreach ($pData['variants'] as $vData) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $vData['size'],
                    'price' => $vData['price'],
                    'stock' => 50,
                    'status' => 'active',
                ]);
            }
        }
    }
}
