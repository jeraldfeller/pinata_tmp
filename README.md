# Cron jobs

*/2 * * * * /home/[user]/application/current/app/console swiftmailer:spool:send --message-limit=10 --env=prod >/dev/null
