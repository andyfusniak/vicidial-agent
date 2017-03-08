# CHANGELOG
## 1.6.0 (8 Mar 2017)\
- Ignore flagged duplicated using the optional is_dup column
- Db Filter to filter lists on date_added

## 1.5.2 (6 Mar 2017)
- Bugfix 1.5.1 release (see below)

## 1.5.1 (6 Mar 2017)
- Change composer.json to pull vicidial-api-gateway direct from bitbucket

## 1.5.0 (6 Mar 2017)
- Use port 8080 for VICIdial api gateway connection as 3BB doesn't support port 80.  (requires vicidial-api-gateway 1.1.0)

## 1.4.0 (28 Feb 2017)
- Make sure at least one page of actual data is pushed per program run

## 1.3.0 (28 Feb 2017)
- Process only one page of items per program run

## 1.2.0 (28 Feb 2017)
- Daily log rotation to log to ./var/logs directory

## 1.1.2 (28 Feb 2017)
- Fix vagent source adapter

## 1.1.1 (28 Feb 2017)

- Fix bug with vagent system database not connecting

## 1.1.0 (28 Feb 2017)

- VAgent program handles multiple source configs one by one
- Exceptions are written to system log and not displayed to stdout

## 1.0.2 (13 Dec 2016)

- Pass 'skip_errors' config.php parameter to the main app
