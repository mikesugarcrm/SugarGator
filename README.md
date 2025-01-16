# SugarGator
### The Sugar Logs Aggregator

## What does it do?
The SugarGator is a custom logger combined with a custom module to provide a familiar interface for working with Sugar's logs.

For every log entry created by Sugar, including the $GLOBALS['log']->fatal() method, the SugarGator can create a bean record in the sg_LogsAggregator module.
These records include all data that would be logged, including the user, pid, channel, of course the log message itself. 
These can be sorted, searched and filtered for ease of access.

This repo has everything you need to create a Module Loadable Package that will set up everything you need.

## How to get started:
1. Clone the repo locally.<br>
2. Zip the repo's contents into an MLP:<br/>
 `zip  -r sugagator.zip custom/ SugarModules/* icons/* scripts/* manifest.php`<br/>
3. Install the MLP in your Sugar instance.

## What does installing the MLP do?
- It creates the custom module sg_LogsAggregator ("Logs").
- It makes the various fields on the Logs bean searchable and filterable.
- It creates the custom logger SugarGator and its Factory and Handler.
- It searches your $sugar_config for any channel loggers and adds the SugarGator as a new handler for all of them.
- It creates ACL's restricting access to the sg_LogsAggregator module to admins only.
- It creates a scheduler job to prune older log records and log records that exceed a configurable maximum number of logs for a given channel.

## How to see the Logs
Log into your sugar instance and navigate to:<br/>
`http://domain.tld/#sg_LogsAggregator`
<br/>There should also be a link in your profile menu if you're an admin.


## How to configure the logs
Update your config_override.php file. Look for:<br/>
```sugar_config['logger']['channels']['<channel_name>']['handlers'][1]['type'] = 'SugarGator';
$sugar_config['logger']['channels']['<channel_name>']['handlers'][1]['name'] = '<channel_name>';
$sugar_config['logger']['channels']['<channel_name>']['handlers'][1]['level'] = 'EMERGENCY';
$sugar_config['logger']['channels']['<channel_name>']['handlers'][1]['max_num_records'] = 100;
$sugar_config['logger']['channels']['<channel_name>']['handlers'][1]['prune_records_older_than_days'] = 2;
```
You can set the level, name, max_num_records and prune_records_older_than_days.
<br/><b>NOTE:</b> 'EMERGENCY' is the "highest", or most restrictive log level for Monolog, and will do the least amount of logging. 
<br/>By default, all SugarGator handlers will be set to 'EMERGENCY' to avoid flooding the DB with log entries.
You may to change this setting to one of the following:
```
DEBUG (maximum logging)
INFO
NOTICE
WARNING
ERROR
CRITICAL
ALERT
EMERGENCY (minimum logging)
```
Which setting you use will depend on which methods your custom loggers and/or sugar stock logger are using. 
For example, if your custom logger is instantiated this way:
```
$logger =  Sugarcrm\Sugarcrm\Logger\Factory::getLogger('logic_hook');
$logger->error("An error occurred.");
```
Then your configs will need to be set to 'ERROR' or lower:
```
$sugar_config['logger']['channels']['logic_hook']['handlers'][1]['level'] = 'ERROR';
```

## How to disable the stock logging ($GLOBALS['log']->method()) from being saved to the DB.
By default, the SugarGator will record in the DB anything that the stock logging would write to the log file.
If you don't want that, you can set a value in config_override.php to disable such logging:
```
$sugar_config['logger']['channels']['sugarcrm']['handlers'][0]['level'] = 'EMERGENCY';
```
This won't affect what gets written to the sugarcrm.log file, and it will prevent the SugarGator from saving those entries to the DB.


## What does this package not do?
- It doesn't call SugarBean->save() to create the records. Too much overhead.
- It doesn't allow log entries to be edited. All fields are read-only.
- It doesn't do any auditing - you can't edit, you don't need to audit.
- It doesn't index log entries into elastic search.
- It doesn't interfere with normal logging to log files. 

## Anything else?
- This module and logging combination may produce a significant performance load. 
    The degree to which the DB logging may impact your instance's performance depends on how many log 
    entries you're writing to the DB in any given session. Try to keep the amount of logging you're doing to a minimum 
    to avoid performance degradation. Deploying to production instances is not advised until you're certain of the performance impact.
- Log entries longer than 4000 characters will be truncated to avoid flooding the DB with huge text fields. 
