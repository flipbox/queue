# Changelog
All Notable changes to `flipboxdigital/queue` will be documented in this file

## 1.0.0-beta.3 - 2017-8-3
### Fixed
- Improper trait method name

### Changed
- `QueueInterface::run()` must return a boolean

### Added
- Better support for multiple queues

## 1.0.0-beta.2 - 2017-6-28
### Changed
- When using multiple queues, passing a null queue index will result in getting the first registered queue.

## 1.0.0-beta.1 - 2017-6-15
### Added
- `JobInterface::toConfig() is responsible for packaging the job for serialization.`

## 1.0.0-beta - 2017-6-5
### Added
- Initial release!