# Luxora — WooCommerce Boutique Theme

A bespoke, production-ready WordPress + WooCommerce theme converted pixel-for-pixel
from the LUXORA React/TanStack boutique design. No page builders — every template is
hand-built PHP with a real Tailwind CSS v4 build, GSAP scroll animations, and AJAX
cart / wishlist.

Built by **WP Spartans**, Dhaka.

---

## Requirements

- WordPress 6.4+
- PHP 7.4+ (tested on 8.3)
- WooCommerce 8.0+ (the storefront pages need it; the theme degrades gracefully with an admin notice if it is inactive)

## Installation

1. In WP Admin → **Appearance → Themes → Add New → Upload Theme**, upload `luxora.zip` and **Activate**.
2. (Optional but recommended) Upload and activate `luxora-child.zip` — the child theme — and do your customisations there so parent updates never overwrite them.
3. Install and activate **WooCommerce**, then run its setup wizard.
4. Set your store currency to **Bangladeshi Taka (৳)** under **WooCommerce → Settings → General** to match the design (any currency works).

## First-run setup

**Menus** (Appearance → Menus): create and assign
- `Primary` — main header navigation
- `Mega` — optional secondary strip under the header
- `Mobile` — optional; falls back to Primary
- `Footer` — footer links

**Pages** — on theme activation, Luxora automatically creates every page it needs
(About, Contact, FAQ, Collections, Best Sellers, New Arrivals, Wishlist, Track Order,
Privacy Policy, Terms) with the correct page template assigned, and builds starter
Primary / Mobile / Footer menus linking them. Nothing is duplicated if a page or menu
already exists. You only need to:
- Set **Cart**, **Checkout**, **My Account** under WooCommerce → Settings → Advanced.
- Set the **Shop** catalog page under WooCommerce → Settings → Products.
- Fill in the Privacy Policy and Terms content.

If you ever need to recreate a page manually, assign its template under Page Attributes →
Template (e.g. **About**, **Contact**, **FAQ**, **Collections**, **Best Sellers**,
**New Arrivals**, **Wishlist**, **Order Tracking**).

**Account screens** — login, registration, forgot/reset password, the dashboard, and
the profile editor are fully custom templates in `woocommerce/myaccount/`. They render
through the **My Account** page, so ensure that page is set and (for the register column)
that registration is enabled under WooCommerce → Settings → Accounts & Privacy.

**Homepage** (Settings → Reading): set a static front page. The theme's `front-page.php`
renders the full editorial homepage (hero, collections, trending, editorial, new
arrivals, why-us, best sellers, brands, reviews, Instagram, newsletter) automatically.

## Customizer

**Appearance → Customize** exposes:
- **Homepage Content** — hero eyebrow / heading / subtitle / image, editorial block, brand list, Instagram handle.
- **Colors** — ink, gold, cream, beige (live preview).
- **Typography** — display / serif / sans families (Google Fonts).
- **Announcement bar**, **currency label**, **social links**, **footer text**, **newsletter** copy.

Product colour swatches read from a product attribute named **Color** (`pa_color`);
add colour terms to show the swatch picker on product pages. Brand shows from a
`pa_brand` attribute or the Customizer brand list.

## Features

- Shared product-card renderer used by the homepage and the WooCommerce loop.
- Custom single-product, cart, mini-cart (slide-in), checkout, thank-you, and My Account templates — all preserving native WooCommerce hooks, gateways, and validation.
- AJAX add-to-cart, live cart quantity/remove, header mini-cart + count fragments.
- Cookie + user-meta wishlist with AJAX toggle and a dedicated Wishlist page.
- Real order-tracking lookup (order number + billing email) mapped to a delivery timeline.
- Shop filtering (category / colour / price) and sort, with a load-more control.
- GSAP ScrollTrigger reveals with a `prefers-reduced-motion` guard.
- SEO: JSON-LD schema, Open Graph, semantic markup.
- Translation-ready (`luxora` text domain), escaped/sanitised output, nonce-protected AJAX.

## Rebuilding the CSS (developers)

Styles compile from `build/input.css` with Tailwind v4, scanning the theme's PHP + JS.

```bash
cd build
npx @tailwindcss/cli -i input.css -o ../assets/css/main.css --minify
```

Edit design tokens (`:root` OKLCH variables, `@theme`, `@utility`) in `input.css`,
then recompile. The output at `assets/css/main.css` is what the theme enqueues.

## File map

```
luxora/
├── functions.php            Bootstraps /inc modules
├── inc/                     setup, enqueue, template-tags, customizer,
│                            widgets, seo-schema, wishlist, ajax, woocommerce
├── template-parts/          home/ + content/ sections, header drawer/search/minicart
├── woocommerce/             cart/ checkout/ myaccount/ single-product/ loop/ global/
├── template-wishlist.php    Page template: Wishlist
├── template-track.php       Page template: Order Tracking
├── assets/css/main.css      Compiled Tailwind bundle
├── assets/js/               main.js, customizer-preview.js
└── build/input.css          Tailwind source (recompile as above)
```

## Support

Questions or changes: WP Spartans.
