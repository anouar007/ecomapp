<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class ChatbotController extends Controller
{
    /**
     * Handle a chatbot message from the authenticated user.
     */
    public function ask(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        // Rate limit: max 15 requests per minute per user
        $key = 'chatbot:' . auth()->id();
        if (RateLimiter::tooManyAttempts($key, 15)) {
            return response()->json([
                'reply' => "Vous envoyez trop de messages. Veuillez patienter une minute avant de réessayer."
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $apiKey = config('services.openai.key');

        // Fallback if no API key is configured
        if (empty($apiKey)) {
            return response()->json([
                'reply' => $this->fallbackReply($request->message)
            ]);
        }

        $appName   = setting('app_name', 'notre boutique');
        $shopUrl   = url('/shop');
        $phone     = setting('company_phone', 'non disponible');
        $email     = setting('company_email', 'non disponible');
        $currency  = setting('currency_code', 'MAD');

        $systemPrompt = <<<PROMPT
Tu es un assistant client IA pour {$appName}, une boutique en ligne spécialisée dans les équipements d'impression grand format au Maroc (imprimantes éco-solvant, traceurs de découpe, encres, consommables et accessoires).

Informations importantes :
- Boutique : {$appName}
- Catalogue : {$shopUrl}
- Téléphone : {$phone}
- Email : {$email}
- Devise : {$currency}
- Livraison : partout au Maroc, J+1 dans les grandes villes
- Installation : incluse avec chaque machine pour les grandes villes
- Formation opérateur : gratuite (1-2 jours) à l'installation
- Paiement : virement, chèque, et facilités de paiement disponibles
- Garantie : 1 an sur les pièces et la main d'œuvre

Fonctionnalités de l'espace client :
- "Mes commandes" : consulter l'historique et le statut de vos commandes
- "Paramètres du profil" : modifier nom, email et mot de passe
- Pour passer une commande : aller dans la boutique, ajouter au panier, et valider

Règles :
- Réponds TOUJOURS en français, de manière concise et amicale
- Ne révèle jamais de données personnelles d'autres clients
- Si tu ne sais pas, propose de contacter le support via {$phone} ou {$email}
- Maximum 3-4 phrases par réponse
PROMPT;

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
                'model'       => 'gpt-4o-mini',
                'max_tokens'  => 256,
                'temperature' => 0.7,
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $request->message],
                ],
            ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content', '');
                return response()->json(['reply' => trim($reply)]);
            }

            return response()->json([
                'reply' => "Je rencontre une difficulté technique. Veuillez contacter notre support au {$phone}."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'reply' => "Service temporairement indisponible. Veuillez réessayer ou contacter le support."
            ]);
        }
    }

    /**
     * Simple rule-based fallback when no API key is set.
     */
    private function fallbackReply(string $message): string
    {
        $msg = mb_strtolower($message);
        $phone = setting('company_phone', '');
        $email = setting('company_email', '');

        if (str_contains($msg, 'commande') || str_contains($msg, 'order')) {
            return "Pour passer une commande, rendez-vous dans notre **Boutique**, ajoutez vos articles au panier et validez le paiement. Pour suivre vos commandes existantes, consultez la section **Mes commandes** dans votre espace client.";
        }
        if (str_contains($msg, 'livraison') || str_contains($msg, 'délai') || str_contains($msg, 'livr')) {
            return "Nous livrons partout au Maroc ! Les grandes villes sont livrées en **J+1** avec installation sur site incluse. Contactez-nous au {$phone} pour plus de détails.";
        }
        if (str_contains($msg, 'retour') || str_contains($msg, 'remboursement') || str_contains($msg, 'rembours')) {
            return "Pour toute demande de retour ou de remboursement, contactez notre service client au **{$phone}** ou par email à **{$email}**. Nous traitons les demandes sous 48h ouvrées.";
        }
        if (str_contains($msg, 'paiement') || str_contains($msg, 'prix') || str_contains($msg, 'cost')) {
            return "Nous acceptons le **virement bancaire**, le **chèque** et proposons des **facilités de paiement** adaptées à votre situation. Contactez-nous pour un devis personnalisé.";
        }
        if (str_contains($msg, 'formation') || str_contains($msg, 'installer') || str_contains($msg, 'installation')) {
            return "L'installation et la **formation opérateur gratuite** (1 à 2 jours selon la machine) sont incluses avec chaque équipement pour les grandes villes marocaines.";
        }
        if (str_contains($msg, 'garantie') || str_contains($msg, 'sav') || str_contains($msg, 'panne')) {
            return "Toutes nos machines bénéficient d'une **garantie 1 an** (pièces + main d'œuvre). Notre SAV est disponible au {$phone} du lundi au vendredi.";
        }
        if (str_contains($msg, 'profil') || str_contains($msg, 'mot de passe') || str_contains($msg, 'compte')) {
            return "Pour modifier votre profil ou changer votre mot de passe, accédez à la section **Paramètres du profil** dans le menu de gauche de votre espace client.";
        }
        if (str_contains($msg, 'contact') || str_contains($msg, 'joindre') || str_contains($msg, 'téléphone')) {
            return "Vous pouvez nous joindre par **téléphone au {$phone}** ou par **email à {$email}**. Nous sommes disponibles du lundi au vendredi de 9h à 18h.";
        }
        if (str_contains($msg, 'encre') || str_contains($msg, 'consommable') || str_contains($msg, 'produit')) {
            return "Nous proposons une large gamme d'**encres d'origine et compatibles certifiées** (Roland, Epson, Mimaki...), ainsi que tous les consommables pour votre atelier. Consultez notre boutique !";
        }

        return "Bonjour ! Je suis l'assistant de " . setting('app_name', 'notre boutique') . ". Je peux vous aider avec vos commandes, la livraison, les produits, ou l'utilisation de votre espace client. Comment puis-je vous aider ?";
    }
}
