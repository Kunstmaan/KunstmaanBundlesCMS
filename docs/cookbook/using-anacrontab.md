Using anacrontab
=====================

## What is Cron?

Cron is a scheduling utility that allows you to assign tasks to run at preconfigured times. A basic tool, cron can be utilized to automate almost anything on your system that must happen at regular intervals.

Equally adept at managing task that must be performed hourly or daily and large routines that must be done once or twice a year, cron is an essential tool for a system administrator.

## Using Anacron with Cron

One of cron's biggest weaknesses is that it assumes that your server or computer is always on. If your machine is off and you have a task scheduled during that time, the task will never run.

This is a serious problem with systems that cannot be guaranteed to be on at any given time. Due to this scenario, a tool called "anacron" was developed. Anacron stands for anachronistic, and it is used compensate for this problem with cron.

Anacron uses parameters that are not as detailed as cron's options. The smallest increment that anacron understands is days. This means that anacron should be used to complement cron, not to replace it.

Anacron's advantage is that it uses time-stamped files to find out when the last time its commands were executed. This means, if a task is scheduled to be run daily and the computer was turned off during that time, when anacron is run, it can see that the task was last run more than 24 hours ago and execute the task correctly.

Our anacrontab default example can be found in the app/config from the standard edition.
