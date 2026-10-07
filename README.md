# Prebuilt ACF Blocks

A growing collection of ready-to-use custom blocks for classic WordPress themes, built with ACF PRO. Each block lives in its own folder with its template, CSS, JavaScript, preview image and ACF field group, so you can copy only the blocks you need.

## Blocks

| Block | What it does |
| --- | --- |
| [Scrolling Text with Image](blocks/scrolling-text-with-image) | Sticky scroll section: text sections scroll on the right while the matching image changes on the left |

## Requirements

- WordPress 5.9 or newer
- [ACF PRO](https://www.advancedcustomfields.com/pro/)
- A classic theme or child theme where you can edit `functions.php`

## Repository structure

```
prebuilt-acf-blocks/
├── shared/
│   └── inc/render-acf-blocks.php        shared setup (category, icon, render and enqueue callbacks)
└── blocks/
    └── <block-name>/
        ├── README.md                    block documentation
        ├── register.php                 the acf_register_block_type() call to copy
        ├── template-parts/blocks/section_<block-name>.php
        ├── css/block_<block-name>.css
        ├── js/blocks_js/block_<block-name>.js
        ├── assets/block-previews/<block-name>.jpg
        └── acf-json/<block-name>.json   ACF field group
```

Inside a block folder, `template-parts/`, `css/`, `js/` and `assets/` use the same paths as in a theme, so you copy them into your theme root as they are.

## Installation

### Step 1: Set up the shared code (once per theme)

Choose the case that matches your theme.

#### A. Your theme has no ACF block render file yet

1. Copy `shared/inc/render-acf-blocks.php` to `your-theme/inc/render-acf-blocks.php`.
2. Load it in `functions.php`:

```php
require_once get_theme_file_path( '/inc/render-acf-blocks.php' );
```

3. Open the block's `register.php`, copy the `acf_register_block_type( array( ... ) );` call, and paste it inside `gutenbergtheme_register_blocks()` in that file (where the comment says to paste it).

#### B. Your theme already includes a render file (for example `render-acf-blocks.php`)

**Do not copy the shared file.** Both files declare the same functions, and WordPress stops with a fatal "Cannot redeclare ..." error. Add only the block registration to your existing file:

1. Open the block's `register.php` and copy the `acf_register_block_type( array( ... ) );` call. Copy only that call, not the opening `<?php` or the comment.
2. Paste it inside your existing `acf/init` function, next to your other `acf_register_block_type()` calls.
3. Check these four values in the pasted call and match them to your setup:

| Setting | In the snippet | What to do |
| --- | --- | --- |
| `category` | `themeblock` | Use your own block category slug, or register a category with this slug |
| `icon` | `THEME_BLOCK_ICON` | Use your own icon, a Dashicon name (for example `'layout'`), or define the `THEME_BLOCK_ICON` constant |
| `render_callback` | `gutenbergtheme_acf_block_render_callback` | Use your own render callback. It must include `template-parts/blocks/section_{block-name}.php` |
| `enqueue_assets` | `gutenbergtheme_acf_block_enqueue_assets` | Use your own enqueue function. It must load `css/block_{block-name}.css` and `js/blocks_js/block_{block-name}.js` when they exist. You can also copy these two functions from the shared file |

4. The snippet has an `example` array for the hover preview image in the inserter. It only works if your render callback has the `is_inserter_preview` check (see `shared/inc/render-acf-blocks.php`). If yours doesn't, delete the `example` array from the snippet. The block works fine without it.

If your callbacks use a different file naming, rename the block files to match your naming instead of editing the block.

### Step 2: Copy the block files

From `blocks/<block-name>/`, copy these folders into your theme root and keep their structure:

- `template-parts/`
- `css/`
- `js/`
- `assets/` (optional, only for the inserter hover preview)

Do not copy `README.md`, `register.php` or `acf-json/`. The ACF field group is imported in the next step.

### Step 3: Import the ACF field group

**Option A: import once**

1. In WP Admin open **Custom Fields → Tools → Import Field Groups**.
2. Choose `blocks/<block-name>/acf-json/<block-name>.json` and click **Import**.

**Option B: ACF Local JSON**

1. Copy the JSON file into an `acf-json` folder in your theme.
2. Open **Custom Fields → Field Groups** and click **Sync** on the group.

### Step 4: Use the block

Edit any page, click **+**, open the **Khalid Blocks** category and add the block. You can rename the category in `gutenbergtheme_register_block_category()`.

## Naming convention

Everything is connected by the block name. For a block registered with `'name' => 'my-block'`, the files must be called:

| File | Path |
| --- | --- |
| Template | `template-parts/blocks/section_my-block.php` |
| Stylesheet | `css/block_my-block.css` |
| Script | `js/blocks_js/block_my-block.js` |
| Preview image | `assets/block-previews/my-block.jpg` |

## Notes

- **Function prefix:** the shared file uses the `gutenbergtheme_` prefix and the `gutenbergtheme` text domain. Search and replace them with your own if you like.
- **Select field choices:** the shared file fills the choices of every ACF select field whose name ends with `fontsize` or `gradient`, site-wide. Make that condition more specific if it clashes with your other fields.
- **Script dependencies:** block scripts load without dependencies. A block that needs jQuery can add it with the `gutenbergtheme_block_script_deps` filter.
- **Block CSS needs your theme:** blocks use utility classes such as `fontsize-*` and CSS variables such as `--wp--preset--color--red`. Each block's README lists what it expects.

## Troubleshooting

- **"Cannot redeclare ..." fatal error:** the shared file and your own render file are both loaded. Use case B above and remove the shared file.
- **Block doesn't appear in the inserter:** check that ACF PRO is active and that your register function is hooked to `acf/init`.
- **Block has no styling or JavaScript:** file names must match the block name exactly. Clear any cache plugin or CDN after copying files.
- **Hover preview shows an empty block:** your render callback doesn't have the `is_inserter_preview` check. Add it or remove the `example` array from the snippet.
- **Hover preview is slow in the editor:** keep the preview image small (about 800px wide, under 100 KB).

## Adding a new block to this repository

1. Create `blocks/<new-block-name>/` with the same structure as an existing block.
2. Add a `register.php` containing only the `acf_register_block_type()` call.
3. Export the ACF field group to `acf-json/`.
4. Add a preview image to `assets/block-previews/`.
5. Write a short `README.md` for the block and add a row to the Blocks table above.

## License

MIT
