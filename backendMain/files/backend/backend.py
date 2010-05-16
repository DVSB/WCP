#!/usr/bin/python -O

import sys
from framework.wcfconfig import wcfconfig
from framework.database import database
from framework.configuration import configuration
from framework.jobhandler import jobhandler
from framework.env import env

if len(sys.argv) == 2:
    path = sys.argv[1]
else:
    path = ".."

wcfconfig = wcfconfig(path)
db = database(wcfconfig)
config = configuration(db, wcfconfig)

env = env(db, config)

jh = jobhandler(env)

jh.findPendingJobs()

jh.firePendingJobs()

jh.finishJobs()