# SugarGator
### The Sugar Logs Aggregator

## What does it do?
The SugarGator is a custom logger combined with a custom module to provide a familiar interface for working with Sugar's logs.

For every log entry created by Sugar, including the $GLOBALS['log']->fatal() method, the SugarGator will create a bean record in the sg_LogsAggregator module.
These records include all of the data that would be logged, including the user, pid, channel, of course the log message itself. 
These can be sorted, searched and filtered for ease of access.

This repo has everything you need to create a Module Loadable Package that will set up everything you need.

## How to get started:

1. Clone the repo locally.<br>
2. Zip the repo's contents into an MLP:<br/>
 `zip -r --exclude=LICENSE --exclude=README.md /some/path/you/like/sugagator.zip *`<br/>
3. Install the MLP in your Sugar instance.

## What does that do?
- It creates the custom module sg_LogsAggregator ("Logs").
- It makes the various fields on the bean searchable and filterable.
- It creates the custom logger SugarGator and its Factory and Handler.
- It searches your $sugar_config for any channel loggers and adds the SugarGator as a new handler for all of them.
- It creates ACL's restricting access to the sg_LogsAggregator module to admins only.
- It creates a scheduler job to prune older log records and log records that exceed a configurable maximum number of logs for a given channel.

## How to see the Logs
Log into your sugar instance and navigate to:<br/>
`http://domain.tld/#sg_LogsAggregator`


## How to configure the logs
Update your config_override.php file. Look for:<br/>
```sugar_config['logger']['channels']['<channel_name>']['handlers'][1]['type'] = 'SugarGator';
$sugar_config['logger']['channels']['<channel_name>']['handlers'][1]['name'] = '<channel_name>';
$sugar_config['logger']['channels']['<channel_name>']['handlers'][1]['max_num_records'] = 100;
$sugar_config['logger']['channels']['<channel_name>']['handlers'][1]['prune_records_older_than_days'] = 2;
```
You can set the level, name, max_num_records and prune_records_older_than_days.


## What does it not do?
- It doesn't call SugarBean->save() to create the records. Too much overhead.
- It doesn't allow log entries to be edited. All fields are read-only.
- It doesn't do any auditing - you can't edit, you don't need to audit.
- It doesn't index log entries into elastic search.
- It doesn't interfere with normal logging to log files. 

## Anything else?
- This module and logging combination produces a significant performance load. Deploying to production instances is not advised.
- Log entries longer than 4000 characters will be truncated to avoid flooding the DB with huge text fields. 
