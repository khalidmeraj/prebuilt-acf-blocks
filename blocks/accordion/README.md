# Accordion

An accessible FAQ-style accordion with an optional heading and description above it. Each question is a real button, one answer is open at a time, and the questions are numbered (01, 02, 03, ...).

## Installation

Follow the steps in the [main README](../../README.md#installation). Block name: `accordion`.

## Fields

Check the exact field types in `acf-json/accordion.json`.

| Field name | Type | Notes |
| --- | --- | --- |
| `section_title` | Text | Optional heading. Basic HTML such as `<br>` or `<em>` is allowed |
| `section_title_tag` | Select | Heading tag (`h2`, `h3`, ...) |
| `section_title_fontsize` | Select | Choices are filled in by the shared render file |
| `section_description` | WYSIWYG | Optional text under the heading |
| `section_description_fontsize` | Select | Choices are filled in by the shared render file |
| `inner_container_width` | Select | Adds a width class to the inner `.container` |
| `background_image` | Image | Optional section background |
| `accordion` | Repeater | One row per question |
| `accordion → question` | Text | Question (button label). Rows without a question are skipped |
| `accordion → answer_content` | WYSIWYG | Answer text |

The block also supports the standard block settings: alignment (wide and full), anchor, additional CSS class, text and background colour, and font size.

## What your theme needs to provide

The block assumes a few utility classes from the theme. Without them it still works but looks plainer.

- **Layout classes:** `container`, the width classes you use for `inner_container_width`, and `global-block-ptb` (vertical spacing of a block).
- **Typography and colour classes:** `font-family-primary`, `ftw-medium`, `textcolor-black` and the `fontsize-*` classes that match the font size choices.
- **Optional animation classes:** `animated`, `fadeInUp` and `delay-100ms`. Skip them if you don't use them.
- **No jQuery** and no animation libraries. The block JavaScript is plain vanilla JS.

## Behaviour notes

- **One item open at a time:** opening a question closes the others.
- **Accessibility:** each question is a `<button>` with `aria-expanded` and `aria-controls`. Each answer is a `role="region"` panel that stays `hidden` until it is opened.
- **Multiple accordions per page** work independently, because every item gets an ID based on the block ID.

## Troubleshooting

- **Answers never open:** check that the block JavaScript file is `js/blocks_js/block_accordion.js` and that the browser console shows no errors.
- **Answers are always visible:** your theme CSS may override the `hidden` attribute. Make sure `.accordion_content[hidden]` stays `display: none`.
