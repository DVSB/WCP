#!/usr/bin/python -O

import sys
from framework import *

if len(sys.argv) == 2:
    dbconfig = sys.argv[1]
else:
    dbconfig = '../config.inc.php'

db = database.database(dbconfig)

config = configuration.configuration(db)

jh = jobhandler.jobhandler(db, config)

jh.findPendingJobs()

jh.firePendingJobs()

jh.finishJobs()