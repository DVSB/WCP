#!/usr/bin/python -O

import sys
from framework.database import *
from framework.configuration import *
from framework.jobhandler import *

if len(sys.argv) == 2:
    dbconfig = sys.argv[1]
else:
    dbconfig = '../config.inc.php'

db = database(dbconfig)

config = configuration(db)

jh = jobhandler(db, config)

jh.findPendingJobs()

jh.firePendingJobs()

jh.finishJobs()