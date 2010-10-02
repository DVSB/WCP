
_TRUE_VALUES = ('yes', 'true', 'enabled', 'on', 'aye', '1', 1, True)

class configuration(object):

	def __init__(self, db, wcfconfig):
		self.db = db
		self.wcf = wcfconfig
		self.read()

	def read(self):
		packages = self.db.query('SELECT          optionID\
                        		  FROM            wcf'+ self.db.wcfnr +'_option acp_option,\
                                        		  wcf'+ self.db.wcfnr +'_package_dependency package_dependency\
                        		  WHERE           acp_option.packageID = package_dependency.dependency\
                                        		  AND package_dependency.packageID = ' + self.wcf.package_id + '\
                        		  ORDER BY        package_dependency.priority')
		p = ''
		for package in packages:
			p += str(package[0]) + ','

		self.packages = p.rstrip(',')
		
		configs = self.db.query('SELECT 	optionName, categoryName, optionType, optionValue, optionID \
								 FROM 		wcf' + self.db.wcfnr + '_option\
								 WHERE 		optionID IN (' + self.packages + ')\
								 ORDER BY 	packageID, optionName')
		self.config = {}
		self.section = {}

		for c in configs:
			if self.section.has_key(c[1]) == False:
				self.section[c[1]] = []

			option = c[0].lower()

			self.section[c[1]].append(option)
			
			self.config[option] = [c[2],c[3],c[4]]

	def getSection(self, section):
		if self.section.has_key(section) == True:
			ret = []
			for sec in self.section[section]:
				ret.append({sec: self.config[sec]})
			return ret
		else:
			return None
		
	def set(self, option, value):
		option = option.lower()
		if self.config.has_key(option):
			self.config[option][1] = value
			config = self.config[option]
			if config[0] == 'boolean':
				value = bool(value)
				if value:
					value = '1'
				else:
					value = '0'
			
			sql = "UPDATE 	wcf"+self.db.wcfnr+"_option \
				   SET 		optionValue = '" + str(value) + "' \
				   WHERE 	optionID = " + str(config[2])
			self.db.query(sql)
			
	def get(self, option):
		option = option.lower()
		if self.config.has_key(option):
			config = self.config[option]
			if config[0] == 'boolean':
				if config[1] in _TRUE_VALUES:
					return True
				else:
					return False
			elif config[0] == 'integer':
				if (config[1] == ''):
					return 0
				return int(config[1])
			elif config[0] == 'float':
				return float(config[1])
			else:
				return config[1]
		else:
			return None