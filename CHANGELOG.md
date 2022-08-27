# Release Notes

## [unreleased](https://github.com/pboivin/flou/compare/v1.2.0...main) - next

#### Added

- Add shortcut for single image widths configuration (`ImageSet`)


## [v1.2.0](https://github.com/pboivin/flou/compare/v1.1.0...v1.2.0) - 2022-08-24

#### Added

- Accept render options as array (`ImageFactory`)
- Add missing setter for padding element CSS class (`ImgRenderable`)

#### Changed

- Ensure base64 LQIP is usable with wrapper option (`ImgRenderable`)

#### Chores

- Document various configuration options


## [v1.1.0](https://github.com/pboivin/flou/compare/v1.0.1...v1.1.0) - 2022-08-10

#### Added

- Add option to use Base64 encoded URL as LQIP

#### Changed

- Use GIF format for LQIP in default Glide parameters

#### Chores

- Update README examples and presentation
- Update test helpers


## [v1.0.1](https://github.com/pboivin/flou/compare/v1.0.0...v1.0.1) - 2022-08-04

#### Fixed

- Update glide version constraints to allow installation on a wider range of projects


## [v1.0.0](https://github.com/pboivin/flou/compare/v1.0.0-beta...v1.0.0) - 2022-08-01

#### Added

- Add `data()` method (`ImageSet`)
- Add `toArray()` methods (`Image` and `ImageFile`)

#### Changed

- Export plain array from `toArray()` method (`ImageSet`)
- Change internal setter methods from public to protected (`ImageSet`)
- Rename `source()` to `main()` to improve clarity (`ImgRenderable`)
- Get dimensions from last source item instead of original file (`ImageSetRender`)

#### Chores

- Update lint config and reformat
- Update README examples


## [v1.0.0-beta](https://github.com/pboivin/flou/compare/v0.1.1...v1.0.0-beta) - 2022-07-28

v1 is a complete rewrite of the project with the following goals:

- Optimize for prototyping and static site generators use cases
- Use Glide for image processing
- Support PHP >= 8.0
- Cleaner code architecture
- Fast and decoupled suite of tests


## [v0.1.1](https://github.com/pboivin/flou/compare/v0.1...v0.1.1) - 2020-05-29

Bugfix: Initialize an image processor instance only if the image needs to be processed.


## [v0.1](https://github.com/pboivin/flou/releases/tag/v0.1) - 2020-05-24

Initial release.
