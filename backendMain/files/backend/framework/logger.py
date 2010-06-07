'''
Created on 07.06.2010

@author: toby
'''

class logger(object):
    '''
    classdocs
    '''

    def __init__(self, db):
        self.db = db
        self.log = []
        
        self.sessionID = self.env.db.query("INSERT INTO cp" + self.env.cpnr + "_jobhandler_task_log \
                                            SET execTimeStart = UNIX_TIMESTAMP()")
        
    def append(self, text):
        self.log.append(text)
        
    def writeJobs(self, jobs):
        self.env.db.query("UPDATE cp" + self.env.cpnr + "_jobhandler_task_log \
                           SET execJobhandler = '" + ", ".join(jobs) + "' \
                           WHERE jobhandlerTaskLogID = " + self.sessionID)
        
    def write(self):
        self.env.db.query("UPDATE cp" + self.env.cpnr + "_jobhandler_task_log \
                           SET data = '" + "\n".join(self.log) + "' \
                           WHERE jobhandlerTaskLogID = " + self.sessionID)
        
    def close(self, success):
        self.env.db.query("UPDATE cp" + self.env.cpnr + "_jobhandler_task_log \
                           SET data = '" + "\n".join(self.log) + "', \
                               success = " + int(success) + ", \
                               execTimeEnd = UNIX_TIMESTAMP() \
                           WHERE jobhandlerTaskLogID = " + self.sessionID)