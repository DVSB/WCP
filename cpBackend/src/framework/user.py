

class user(object):
    
    def __init__(self, config):
        self.config = config
        self.db = config.db       
        
    def getAllActiveUsers(self):
        self.users = self.db.queryDict("SELECT     cp_user.*, cp_user.userID AS cpUserID,  \
                                                   GROUP_CONCAT(DISTINCT groups.groupID ORDER BY groups.groupID ASC SEPARATOR ',') AS groupIDs,\
                                                   GROUP_CONCAT(DISTINCT languages.languageID ORDER BY languages.languageID ASC SEPARATOR ',') AS languageIDs,user_option.*,\
                                                   user.*\
                                       FROM        wcf"+self.db.wcfnr+"_user user\
                                       LEFT JOIN   cp"+self.db.cpnr+"_user cp_user ON (cp_user.userID = user.userID)  \
                                       LEFT JOIN   wcf"+self.db.wcfnr+"_user_to_groups groups ON (groups.userID = user.userID) \
                                       LEFT JOIN   wcf"+self.db.wcfnr+"_user_to_languages languages ON (languages.userID = user.userID) \
                                       LEFT JOIN   wcf"+self.db.wcfnr+"_user_option_value user_option ON (user_option.userID = user.userID)\
                                       WHERE       banned = 0  \
                                       GROUP BY    user.userID")
        
    def getUserData(self, userID):
        return self.users[userID]
