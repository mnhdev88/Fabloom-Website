#!/bin/bash
# Auto-deploy from GitHub, run by cPanel cron.
#
# Pulls main into the cPanel Git Version Control clone and, only when that
# brings in a new commit, runs the .cpanel.yml tasks — the same thing as
# pressing "Update from Remote" then "Deploy HEAD Commit" in cPanel.
#
# Cron entry (cPanel > Advanced > Cron Jobs), every 5 minutes:
#   */5 * * * * /bin/bash /home/thefablo/repositories/fabloom-website/deploy/auto-deploy.sh
#
# This file lives in the repo but is not copied into public_html by
# .cpanel.yml, so it is never reachable from the web.

REPO=/home/thefablo/repositories/fabloom-website
LOG=/home/thefablo/logs/auto-deploy.log
LOCK=/tmp/fabloom-auto-deploy.lock

mkdir -p "$(dirname "$LOG")"

# A slow deploy must not overlap the next cron run.
exec 9>"$LOCK"
flock -n 9 || exit 0

cd "$REPO" || { echo "$(date '+%F %T') repo not found at $REPO" >>"$LOG"; exit 1; }

before=$(git rev-parse HEAD)
if ! git pull --ff-only --quiet origin main >>"$LOG" 2>&1; then
    # Usually a hand edit inside the clone. cPanel will not deploy from a dirty
    # tree either, so stop and leave it for a person to look at.
    echo "$(date '+%F %T') git pull failed — check 'git status' in $REPO" >>"$LOG"
    exit 1
fi
after=$(git rev-parse HEAD)

[ "$before" = "$after" ] && exit 0

echo "$(date '+%F %T') deploying $(git log -1 --format='%h %s')" >>"$LOG"
uapi --output=json VersionControlDeployment create repository_root="$REPO" >>"$LOG" 2>&1
echo "$(date '+%F %T') deploy queued for $after" >>"$LOG"
