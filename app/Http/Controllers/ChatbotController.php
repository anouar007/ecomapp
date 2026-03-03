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
        $key = 'chatbot:' . (auth()->id() ?? 'guest');
        if (RateLimiter::tooManyAttempts($key, 15)) {
            return response()->json([
                'reply' => "Vous envoyez trop de messages. Veuillez patienter une minute avant de réessayer."
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $appName  = setting('app_name', 'cette boutique');
        $shopUrl  = url('/shop');
        $phone    = setting('company_phone', '');
        $email    = setting('company_email', '');
        $currency = setting('currency_code', 'MAD');
        $isAdmin  = auth()->user()?->hasRole('Admin') ?? false;

        $systemPrompt = $isAdmin
            ? "Tu es un assistant IA expert pour les administrateurs de {$appName}, une boutique e-commerce d'équipements d'impression grand format au Maroc. Tu aides avec : gestion des produits, commandes, coupons, inventaire, clients, rapports, paramètres et l'utilisation du tableau de bord. Réponds en français, de façon concise (max 4 phrases). Si tu ne sais pas, dis-le honnêtement."
            : "Tu es un assistant client pour {$appName}, une boutique d'équipements d'impression grand format au Maroc. Infos clés: livraison J+1 grandes villes, installation gratuite, formation incluse, garantie 1 an, paiement par virement/chèque/facilités. Contact: {$phone} / {$email}. Réponds en français, de façon concise (max 4 phrases).";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $request->message],
        ];

        // ── 1. Try Ollama (local, free, privacy-first) ───────────────────────
        try {
            $ollamaResponse = Http::timeout(30)->post('http://localhost:11434/api/chat', [
                'model'    => config('services.ollama.model', 'llama3.2'),
                'messages' => $messages,
                'stream'   => false,
            ]);

            if ($ollamaResponse->successful()) {
                $reply = $ollamaResponse->json('message.content', '');
                if (!empty(trim($reply))) {
                    return response()->json(['reply' => trim($reply)]);
                }
            }
        } catch (\Exception $e) {
            // Ollama not running — fall through to next option
        }

        // ── 2. Try OpenAI (if API key is set) ───────────────────────────────
        $apiKey = config('services.openai.key');
        if (!empty($apiKey)) {
            try {
                $openaiResponse = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type'  => 'application/json',
                ])->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
                    'model'      => 'gpt-4o-mini',
                    'max_tokens' => 256,
                    'messages'   => $messages,
                ]);

                if ($openaiResponse->successful()) {
                    $reply = $openaiResponse->json('choices.0.message.content', '');
                    if (!empty(trim($reply))) {
                        return response()->json(['reply' => trim($reply)]);
                    }
                }
            } catch (\Exception $e) {
                // OpenAI failed — fall through to rule-based fallback
            }
        }

        // ── 3. Rule-based fallback (always works) ────────────────────────────
        return response()->json([
            'reply' => $this->fallbackReply($request->message)
        ]);
    }


    /**
     * Comprehensive bilingual (FR + EN) rule-based fallback.
     */
    private function fallbackReply(string $message): string
    {
        $msg   = mb_strtolower($message);
        $name  = setting('app_name', 'notre boutique');
        $phone = setting('company_phone', '');
        $email = setting('company_email', '');

        // Helper: checks if any needle exists in the message
        $has = fn(array $needles) => collect($needles)->contains(fn($n) => str_contains($msg, $n));

        // ── ADMIN: Products ──────────────────────────────────────────────────
        if ($has(['add product', 'new product', 'ajouter produit', 'créer produit', 'create product',
                  'ajouter un produit', 'nouveau produit', 'add a product'])) {
            return "Pour **ajouter un produit**, allez dans **Products → Add Product** dans le menu de gauche. Remplissez le nom, la description, le prix, la catégorie et les images, puis cliquez sur **Save**. Vous pouvez activer/désactiver le produit via le statut.";
        }
        if ($has(['edit product', 'modifier produit', 'update product', 'mettre à jour produit', 'changer produit'])) {
            return "Pour **modifier un produit**, allez dans **Products**, cliquez sur le bouton ✏️ (Edit) à droite du produit concerné, faites vos changements, puis cliquez sur **Update**.";
        }
        if ($has(['delete product', 'supprimer produit', 'remove product', 'effacer produit'])) {
            return "Pour **supprimer un produit**, allez dans **Products**, cliquez sur le bouton 🗑️ (Delete) du produit et confirmez. Attention : cette action est irréversible.";
        }

        // ── ADMIN: Categories ────────────────────────────────────────────────
        if ($has(['category', 'categorie', 'catégorie', 'categories', 'catégories'])) {
            return "Pour gérer les **catégories**, allez dans **Categories** dans le menu de gauche. Vous pouvez y ajouter, modifier ou supprimer des catégories et y attacher des produits.";
        }

        // ── ADMIN: Orders ────────────────────────────────────────────────────
        if ($has(['manage order', 'gérer commande', 'gérer les commandes', 'order management',
                  'update order', 'modifier commande', 'order status', 'statut commande'])) {
            return "Pour **gérer les commandes**, allez dans **Orders** dans le menu. Cliquez sur une commande pour voir les détails, modifier le statut (En attente → Confirmée → Expédiée → Livrée) et imprimer la facture.";
        }

        // ── ADMIN: Invoices ──────────────────────────────────────────────────
        if ($has(['invoice', 'facture', 'invoices', 'factures', 'pdf'])) {
            return "Les **factures** se génèrent automatiquement à chaque commande. Pour les consulter ou les télécharger en PDF, allez dans **Invoices** dans le menu de gauche.";
        }

        // ── ADMIN: Coupons ───────────────────────────────────────────────────
        if ($has(['coupon', 'promo', 'discount', 'réduction', 'code promo', 'promotion',
                  'create coupon', 'créer coupon', 'add coupon'])) {
            return "Pour **créer un coupon**, allez dans **Coupons → Add Coupon**. Définissez un code, le type de réduction (fixe ou %), la valeur, la date d'expiration et la limite d'utilisation. Le client l'entre au moment du paiement.";
        }

        // ── ADMIN: Inventory ─────────────────────────────────────────────────
        if ($has(['inventory', 'stock', 'inventaire', 'quantité', 'quantity', 'rupture'])) {
            return "Pour gérer le **stock**, allez dans **Inventory**. Vous pouvez y ajuster les quantités manuellement, voir les alertes de rupture de stock et exporter l'inventaire.";
        }

        // ── ADMIN: Customers ─────────────────────────────────────────────────
        if ($has(['customer', 'client', 'clients', 'customers', 'user list', 'liste clients'])) {
            return "Pour voir vos **clients**, allez dans **Customers**. Vous pouvez consulter leurs informations, leur historique de commandes, leur ajouter des points de fidélité ou les contacter directement.";
        }

        // ── ADMIN: Reports ───────────────────────────────────────────────────
        if ($has(['report', 'rapport', 'analytics', 'statistique', 'stats', 'ventes', 'revenue', 'chiffre'])) {
            return "Pour les **rapports et statistiques**, allez dans **Reports** dans le menu. Vous y trouverez les ventes par période, les produits les plus vendus, et les revenus par catégorie.";
        }

        // ── ADMIN: Settings ─────────────────────────────────────────────────
        if ($has(['setting', 'paramètre', 'settings', 'configuration', 'config', 'theme',
                  'color', 'couleur', 'logo', 'app name', 'nom app'])) {
            return "Pour configurer l'application, allez dans **Settings** dans le menu. Vous pouvez y changer le nom, logo, couleurs, devise, coordonnées, et bien plus encore.";
        }

        // ── ADMIN: Users & Roles ─────────────────────────────────────────────
        if ($has(['user', 'admin', 'role', 'permission', 'access', 'utilisateur', 'accès',
                  'add user', 'ajouter utilisateur', 'new user'])) {
            return "Pour gérer les **utilisateurs et les rôles**, allez dans **Access Control**. Vous pouvez créer des utilisateurs, leur assigner des rôles (Admin, Manager, etc.) et contrôler leurs permissions.";
        }

        // ── ADMIN: POS ───────────────────────────────────────────────────────
        if ($has(['pos', 'point of sale', 'caisse', 'terminal', 'vente directe'])) {
            return "Le **POS Terminal** (Point de vente) vous permet de créer des ventes directement sans que le client passe par le site. Cliquez sur **POS Terminal** dans le menu vert pour l'ouvrir.";
        }

        // ── SHARED: Orders (customer view) ───────────────────────────────────
        if ($has(['commande', 'order', 'my order', 'ma commande', 'où est', 'track', 'suivi'])) {
            return "Pour **suivre vos commandes**, allez dans la section **Mes commandes** de votre espace client (`/my-account`). Chaque commande indique son statut en temps réel (En attente, Confirmée, Expédiée, Livrée).";
        }

        // ── SHARED: Delivery ─────────────────────────────────────────────────
        if ($has(['livraison', 'délai', 'delivery', 'shipping', 'livrer', 'when will', 'quand'])) {
            return "Nous livrons **partout au Maroc**. Les grandes villes sont livrées en **J+1** avec installation sur site incluse. Contactez-nous au **{$phone}** pour plus de détails.";
        }

        // ── SHARED: Payment ──────────────────────────────────────────────────
        if ($has(['paiement', 'payment', 'pay', 'prix', 'price', 'cost', 'tarif', 'combien', 'how much'])) {
            return "Nous acceptons le **virement bancaire**, le **chèque** et proposons des **facilités de paiement**. Contactez-nous au {$phone} pour un devis personnalisé.";
        }

        // ── SHARED: Contact ──────────────────────────────────────────────────
        if ($has(['contact', 'phone', 'téléphone', 'email', 'joindre', 'reach', 'call', 'appeler', 'mail'])) {
            return "Vous pouvez nous contacter par **téléphone : {$phone}** ou **email : {$email}**. Nous sommes disponibles du lundi au vendredi, 9h–18h.";
        }

        // ── SHARED: Warranty / Support ───────────────────────────────────────
        if ($has(['garantie', 'warranty', 'sav', 'support', 'panne', 'broken', 'repair', 'réparation'])) {
            return "Toutes nos machines bénéficient d'une **garantie 1 an** (pièces + main d'œuvre). Notre SAV est disponible au **{$phone}** du lundi au vendredi. Décrivez le problème par email à {$email} pour un suivi écrit.";
        }

        // ── SHARED: Training / Installation ─────────────────────────────────
        if ($has(['formation', 'training', 'install', 'installation', 'setup', 'apprendre', 'learn'])) {
            return "L'**installation et la formation opérateur** (1 à 2 jours) sont **gratuites** et incluses avec chaque machine pour les grandes villes marocaines.";
        }

        // ── SHARED: Profile / Password ───────────────────────────────────────
        if ($has(['profil', 'profile', 'password', 'mot de passe', 'email change', 'changer email'])) {
            return "Pour modifier votre profil ou changer votre mot de passe, allez dans **Settings → Profile** si vous êtes admin, ou dans **Paramètres du profil** de l'espace client.";
        }

        // ── Catch-all ────────────────────────────────────────────────────────
        $suggestions = auth()->user()?->hasRole('Admin')
            ? "ajouter un produit, créer un coupon, gérer les commandes, voir les rapports, configurer les paramètres"
            : "suivre mes commandes, livraison, paiement, contacter le support";

        return "Je n'ai pas de réponse précise pour **\"{$message}\"** dans ma base de connaissances. Voici quelques sujets sur lesquels je peux vous renseigner : {$suggestions}. Ou contactez-nous directement au **{$phone}**.";
    }
}
