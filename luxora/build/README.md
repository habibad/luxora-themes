# Luxora CSS build

The theme ships a pre-compiled stylesheet at `../assets/css/main.css`, so you only
need this if you want to change styles or design tokens.

```bash
cd build
npm install
npm run build     # one-off compile
npm run watch     # rebuild on save
```

`input.css` holds the full design system: `:root` OKLCH tokens, the `@theme` map,
`@utility` helpers (container-luxe, btn-luxe, link-underline, …), the GSAP reveal
states, and the component layer that styles native WooCommerce markup. Tailwind
scans the theme's PHP and JS (see the `@source` lines) to tree-shake unused classes.
