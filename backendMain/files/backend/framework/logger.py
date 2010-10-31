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
        
        self.sessionID = self.db.insert("cp" + self.db.cpnr + "_jobhandler_task_log", 
                                        {'execTimeStart = UNIX_TIMESTAMP()': 'nativefunc'})
        
    def append(self, text):
        self.log.append(text)
        
    def writeJobs(self, jobs):
        jobString = ""
        for job in jobs:
            jobString += job + "\n"
        
        self.db.update("cp" + self.db.cpnr + "_jobhandler_task_log", 
                       {"execJobhandler": jobString.strip()},
                       {"jobhandlerTaskLogID": self.sessionID})
        
    def write(self):
        self.db.update("cp" + self.db.cpnr + "_jobhandler_task_log",
                        {"data": "\n".join(self.log)},
                        {"jobhandlerTaskLogID": self.sessionID})
        
    def close(self, success):
        self.db.update("cp" + self.db.cpnr + "_jobhandler_task_log",
                        {'data': "\n-------\n".join(self.log), 
                         'success': str(success), 
                         'execTimeEnd = UNIX_TIMESTAMP()': 'nativefunc'},
                        {'jobhandlerTaskLogID': self.sessionID})