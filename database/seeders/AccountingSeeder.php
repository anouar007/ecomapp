<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // CLASS 1: FINANCEMENT PERMANENT (Equity & Long-term Debt)
            ['code' => '1111', 'name' => 'Capital social', 'type' => 'Equity', 'class' => 1],
            ['code' => '1140', 'name' => 'Réserve légale', 'type' => 'Equity', 'class' => 1],
            ['code' => '1191', 'name' => 'Résultat net de l\'exercice (créditeur)', 'type' => 'Equity', 'class' => 1],
            ['code' => '1199', 'name' => 'Résultat net de l\'exercice (débiteur)', 'type' => 'Equity', 'class' => 1],
            ['code' => '1481', 'name' => 'Emprunts auprès des établissements de crédit', 'type' => 'Liability', 'class' => 1],

            // CLASS 2: ACTIF IMMOBILISÉ (Fixed Assets)
            ['code' => '2111', 'name' => 'Frais de constitution', 'type' => 'Asset', 'class' => 2],
            ['code' => '2230', 'name' => 'Fonds commercial', 'type' => 'Asset', 'class' => 2],
            ['code' => '2332', 'name' => 'Matériel et outillage', 'type' => 'Asset', 'class' => 2],
            ['code' => '2340', 'name' => 'Matériel de transport', 'type' => 'Asset', 'class' => 2],
            ['code' => '2351', 'name' => 'Mobilier de bureau', 'type' => 'Asset', 'class' => 2],
            ['code' => '2352', 'name' => 'Matériel de bureau', 'type' => 'Asset', 'class' => 2],
            ['code' => '2355', 'name' => 'Matériel informatique', 'type' => 'Asset', 'class' => 2],

            // CLASS 3: ACTIF CIRCULANT (Current Assets)
            ['code' => '3111', 'name' => 'Marchandises', 'type' => 'Asset', 'class' => 3],
            ['code' => '3121', 'name' => 'Matières premières et fournitures', 'type' => 'Asset', 'class' => 3],
            ['code' => '3421', 'name' => 'Clients', 'type' => 'Asset', 'class' => 3],
            ['code' => '3425', 'name' => 'Clients - Factures à établir', 'type' => 'Asset', 'class' => 3],
            ['code' => '3455', 'name' => 'État - TVA récupérable', 'type' => 'Asset', 'class' => 3],
            ['code' => '3458', 'name' => 'État - Autres comptes débiteurs', 'type' => 'Asset', 'class' => 3],
            ['code' => '3461', 'name' => 'Associés - Comptes d\'apport en société', 'type' => 'Asset', 'class' => 3],

            // CLASS 4: PASSIF CIRCULANT (Current Liabilities)
            ['code' => '4411', 'name' => 'Fournisseurs', 'type' => 'Liability', 'class' => 4],
            ['code' => '4417', 'name' => 'Fournisseurs - Factures non parvenues', 'type' => 'Liability', 'class' => 4],
            ['code' => '4432', 'name' => 'Rémunérations dues au personnel', 'type' => 'Liability', 'class' => 4],
            ['code' => '4441', 'name' => 'Caisse Nationale de Sécurité Sociale (CNSS)', 'type' => 'Liability', 'class' => 4],
            ['code' => '4452', 'name' => 'État - Impôts, taxes et assimilés', 'type' => 'Liability', 'class' => 4],
            ['code' => '4455', 'name' => 'État - TVA facturée', 'type' => 'Liability', 'class' => 4],
            ['code' => '4456', 'name' => 'État - TVA due', 'type' => 'Liability', 'class' => 4],
            ['code' => '4465', 'name' => 'Associés - Comptes courants', 'type' => 'Liability', 'class' => 4],

            // CLASS 5: TRÉSORERIE (Treasury)
            ['code' => '5141', 'name' => 'Banques (soldes débiteurs)', 'type' => 'Asset', 'class' => 5],
            ['code' => '5161', 'name' => 'Caisses', 'type' => 'Asset', 'class' => 5],

            // CLASS 6: CHARGES (Expenses)
            ['code' => '6111', 'name' => 'Achats de marchandises', 'type' => 'Expense', 'class' => 6],
            ['code' => '6121', 'name' => 'Achats de matières premières', 'type' => 'Expense', 'class' => 6],
            ['code' => '6125', 'name' => 'Achats non stockés de matières et fournitures', 'type' => 'Expense', 'class' => 6],
            ['code' => '6131', 'name' => 'Locations et charges locatives', 'type' => 'Expense', 'class' => 6],
            ['code' => '6133', 'name' => 'Entretien et réparations', 'type' => 'Expense', 'class' => 6],
            ['code' => '6134', 'name' => 'Primes d\'assurance', 'type' => 'Expense', 'class' => 6],
            ['code' => '6142', 'name' => 'Transports', 'type' => 'Expense', 'class' => 6],
            ['code' => '6145', 'name' => 'Frais postaux et frais de télécommunications', 'type' => 'Expense', 'class' => 6],
            ['code' => '6147', 'name' => 'Services bancaires', 'type' => 'Expense', 'class' => 6],
            ['code' => '6161', 'name' => 'Impôts et taxes directs', 'type' => 'Expense', 'class' => 6],
            ['code' => '6171', 'name' => 'Rémunération du personnel', 'type' => 'Expense', 'class' => 6],
            ['code' => '6174', 'name' => 'Charges sociales', 'type' => 'Expense', 'class' => 6],
            ['code' => '6193', 'name' => 'Dotations aux amortissements', 'type' => 'Expense', 'class' => 6],

            // CLASS 7: PRODUITS (Revenues)
            ['code' => '7111', 'name' => 'Ventes de marchandises au Maroc', 'type' => 'Revenue', 'class' => 7],
            ['code' => '7113', 'name' => 'Ventes de marchandises à l\'étranger', 'type' => 'Revenue', 'class' => 7],
            ['code' => '7121', 'name' => 'Ventes de biens produits au Maroc', 'type' => 'Revenue', 'class' => 7],
            ['code' => '7124', 'name' => 'Ventes de services produits au Maroc', 'type' => 'Revenue', 'class' => 7],
            ['code' => '7381', 'name' => 'Intérêts et produits assimilés', 'type' => 'Revenue', 'class' => 7],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['code' => $account['code']],
                $account
            );
        }
    }
}
