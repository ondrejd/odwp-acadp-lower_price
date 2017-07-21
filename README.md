# Pravidelné snižování cen

Jednoduchý plugin pro [WordPress][1], který pravidelně snižuje cenu inzerátů vytvořeného pomocí pluginu [Advanced Classifieds &amp; Directory Pro][2].

## Upozornění

__Plugin je vytvořen na míru webu [Nepotrebujem.eu][3], takže je pravděpodobné, že jej pro vaše použití budete muset upravit. Pokud o něco takového máte zájem, můžete si to objednat i přímo od [autora][4].__

__Plugin vyžaduje plugin [Advanced Classifieds & Directory Pro][2] - bez tohoto pluginu nainstalovaného nebudete moci aktivovat tento plugin__.

## Poznámka k implementaci

Plugin předpokládá, že produkty, které plugin <abbr title="Advanced Classifieds & Directory Pro">_ACADP_</abbr> vytváří, jsou rozšířené o hodnoty `price_orig`, `price_reduce` a `price_reduce_days`.

## Popis pluginu

* plugin pravidelně snižuje cenu u inzerátů vytvořených pomocí pluginu <abbr title="Advanced Classifieds & Directory Pro">_ACADP_</abbr>, které obsahují meta hodnoty `price`, `price_orig`, `price_reduce` a `price_reduce_days`,
* plugin umožňuje nastavit, kdy bude příslušna úloha spuštěna (pro spouštění je využita funkce [`wp_cron`][5],
* plugin obsahuje přehledný výpis o provedených změnách cen,
* plugin obsahuje přehledný výpis <abbr title="Advanced Classifieds & Directory Pro">_ACADP_</abbr> inzerátů z hlediska jejich ceny a jejích stavu a vývoje.

## Snímky obrazovek

### Nastavení pluginu

![Nastavení pluginu](screenshot-01.png "Nastavení pluginu")

### Ceny produktů

![Přehled cen produktů](screenshot-02.png "Přehled cen produktů")

## Instalace

Pro správnou funkčnost musíte upravit plugin [Advanced Classifieds & Directory Pro][2] takto:

### Soubor `public/class-acadp-public-user.php` řádek 844

```php
// [ondrejd] - pridani snizovani ceny
$price_reduce = (int) $_POST['price_reduce'];
update_post_meta( $post_id, 'price_reduce', $price_reduce );
$price_reduce_days = (int) $_POST['price_reduce_days'];
update_post_meta( $post_id, 'price_reduce_days', $price_reduce_days );
update_post_meta( $post_id, 'price_orig', $price );
// [ondrejd]
```

### Soubor `public/partials/user/acadp-public-edit-listing-display.php` řádek 243

```html
<!-- [ondrejd] - pridani snizovani ceny -->
<div class="row">
    <div class="col-md-4">
        <!-- Snizit cenu o: 1-50 % -->
        <div class="form-group">
            <label class="control-label" for="acadp-price_reduce"><?php _e( 'Snížit cenu o:', 'advanced-classifieds-and-directory-pro' ); ?></label>
            <div class="input-group">
                <input type="number" name="price_reduce" id="acadp-price_reduce" class="form-control" min="1" max="50" step="1" value="<?php echo ( isset( $post_meta['price_reduce'] ) ) ? esc_attr( $post_meta['price_reduce'][0] ) : esc_attr( '10' ); ?>" aria-describedby="acadp-price_reduce-addon">
                <span class="input-group-addon" id="acadp-price_reduce-addon"><?php _e( '%', 'advanced-classifieds-and-directory-pro' ); ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <!-- Po dobu: 1-30 dni -->
        <div class="form-group">
            <label class="control-label" for="acadp-price_reduce_days"><?php _e( 'Po dobu:', 'advanced-classifieds-and-directory-pro' ); ?></label>
            <div class="input-group">
                <input type="number" name="price_reduce_days" id="acadp-price_reduce_days" class="form-control" min="1" max="30" step="1" value="<?php echo ( isset( $post_meta['price_reduce_days'] ) ) ? esc_attr( $post_meta['price_reduce_days'][0] ) : esc_attr( '10' ); ?>" aria-describedby="acadp-price_reduce_days-addon">
                <span class="input-group-addon" id="acadp-price_reduce_days-addon"><?php _e( 'dní', 'advanced-classifieds-and-directory-pro' ); ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>
<!-- //[ondrejd] -->
```

# TODO

Přehled počátečních úkolů k dokončení __verze 1.0.0__.

* ~~Základní struktura pluginu (včetně závislosti na [_wp_][1] i [_acadp_][2])~~
* ~~Stránka s nastavením pluginu:~~
  - ~~kdy se má spouštět skript na změnu cen~~
* ~~Stránka s přehledem všech [_acadp_][2] inzerátů a stavu a vývoje jejich cen~~
  - ~~přidat podbarvení dle stavu vývoje ceny inzerátu~~
* ~~Samotná funkcionalita pro snižování cen (přes [_WP\_Cron_][5])~~
  - ~~provést snížení cen~~
  - ~~uvést čas tohoto posledního spuštění~~
  - __uložit log o změnách__
* __Stránka s výpisem provedených změn cen__
  - vytvořit tabulku `odwpalp-log` a zároveň i odpovídající třídu `ALP_Log_DbTable`
  - vytvořit třídu `ALP_Log_Screen` (dědice [ALP_Screen_Prototype][8])
  - vytvořit třídu `ALP_Log_Table` (dědice [WP_List_Table][9])
* projít zdrojáky
  - kvůli kvalitě (správné názvy metod, odsazení atp.)
  - zapomenuté komentáře a _TODO_
  - ujistit se, že je možný překlad a vygenerovat adresář `languages` a přidat defaultní `PO` a `POT` soubory
  - smazat soubor `TODO.md` (nebo přesunout do souboru [`README.md`][10])
  - dokončit soubor [`README.md`][10] a zároveň vytvořit zkrácenou anglickou verzi [`README.en.md`][11]
* vypublikovat na [GitHub][6] první release (__verze 1.0.0__)
* napsat příspěvek na [ondrejd.com][7]

[1]: https://wordpress.org/
[2]: https://wordpress.org/plugins/advanced-classifieds-and-directory-pro/
[3]: https://nepotrebujem.eu/
[4]: mailto:ondrejd@gmail.com
[5]: https://developer.wordpress.org/plugins/cron/
[6]: https://github.com/ondrejd/odwp-acadp-lower_price
[7]: https://ondrejd.com/
[8]: https://github.com/ondrejd/odwp-acadp-lower_price/blob/master/src/ALP_Screen_Prototype.php
[9]: https://developer.wordpress.org/reference/classes/wp_list_table/
[11]: https://github.com/ondrejd/odwp-acadp-lower_price/blob/master/README.md


