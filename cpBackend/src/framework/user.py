

class user(object):
    
    def __init__(self, userID, env):
        self.env = env
        self.userID = userID
        self.loadUser()
        
    def loadUser(self):
        self.user = self.env.db.queryDict("SELECT    cp_user.*, user_option.*, user.* \
                                             FROM      wcf" + self.env.wcfnr + "_user user \
                                             JOIN      cp" + self.env.cpnr + "_user cp_user \
                                                     ON (cp_user.userID = user.userID) \
                                             LEFT JOIN wcf" + self.env.wcfnr + "_user_option_value user_option \
                                                     ON (user_option.userID = user.userID) \
                                             WHERE     user.userID = " + self.userID 
                                           )
        
        options = self.env.db.query("SELECT      optionName, optionID, optionType \
                                     FROM        cp" + self.env.wcfnr + "_user_option \
                                     ORDER BY    showOrder")
        
        for o in options:
            optionName = 'userOption' + str(o[1])
            if o[2] == 'boolean':
                if int(self.user[optionName]) == 1:
                    var = True
                else:
                    var = False
            elif o[2] == 'integer':
                if (self.user[optionName] == ''):
                    var = 0
                else:
                    var = int(self.user[optionName])
            elif o[2] == 'float':
                var = float(self.user[optionName])
            else:
                var = self.user[optionName]
            
            self.user[o[0]] = var
            del self.user[optionName]
        
    def get(self, option):
        if self.user.has_key(option):
            return self.user[option]
        else:
            return None
