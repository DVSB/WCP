<div class="border tabMenuContent hidden" id="menuTab-advancedPageMenu-content">
		<div class="container-1">
			<h3 class="subHeadline">{lang}wcf.acp.style.editor.menu.advancedPageMenu{/lang}</h3>
			<p class="description">{lang}wcf.acp.style.editor.menu.advancedPageMenu.description{/lang}</p>
			
			<fieldset>
				<legend>{lang}wcf.acp.style.editor.menu.advancedPageMenu.general{/lang}</legend>
				<div class="formFieldDesc">
					<p>{lang}wcf.acp.style.editor.menu.advancedPageMenu.general.description{/lang}</p>
				</div>
			
				<div class="formGroup">
						<div class="formGroupLabel">
							<label>{lang}wcf.acp.style.editor.padding{/lang}</label>
						</div>
						<div class="formGroupField">
							<fieldset>
								<legend>{lang}wcf.acp.style.editor.padding{/lang}</legend>
					
								<div class="formElement">
									<div class="formFieldLabel">
										<label for="menu-advancedPageMenu-padding">{lang}wcf.acp.style.editor.padding.menu{/lang}</label>
									</div>
									<div class="formField">	
										<input type="text" class="inputText" id="menu-advancedPageMenu-padding" name="variables[menu.advancedPageMenu.padding]" value="{$variables['menu.advancedPageMenu.padding']}" />
										<select name="variables[menu.advancedPageMenu.padding.unit]">
											{htmlOptions values=$units output=$units selected=$variables['menu.advancedPageMenu.padding.unit']}
										</select>
									</div>
								</div>
								
								<div class="formElement">
									<div class="formFieldLabel">
										<label for="menu-advancedPageMenu-item-padding">{lang}wcf.acp.style.editor.padding.item{/lang}</label>
									</div>
									<div class="formField">	
										<input type="text" class="inputText" id="menu-advancedPageMenu-item-padding" name="variables[menu.advancedPageMenu.item.padding]" value="{$variables['menu.advancedPageMenu.item.padding']}" />
										<select name="variables[menu.advancedPageMenu.item.padding.unit]">
											{htmlOptions values=$units output=$units selected=$variables['menu.advancedPageMenu.item.padding.unit']}
										</select>
									</div>
								</div>
																
							</fieldset>
						</div>					
				</div>
			</fieldset>		
			
			<fieldset>
				<legend>{lang}wcf.acp.style.editor.menu.advancedPageMenu.shadow{/lang}</legend>
				<div class="formFieldDesc">
					<p>{lang}wcf.acp.style.editor.menu.advancedPageMenu.shadow.description{/lang}</p>
				</div>
				
				<div class="formGroup">
						<div class="formGroupLabel">
							<label>{lang}wcf.acp.style.editor.offset{/lang}</label>
						</div>
						<div class="formGroupField">
							<fieldset>
								<legend>{lang}wcf.acp.style.editor.offset{/lang}</legend>
								<div class="formElement">
									<div class="formFieldLabel">
										<label for="menu-advancedPageMenu-shadow-size">{lang}wcf.acp.style.editor.shadow.size{/lang}</label>
									</div>
									<div class="formField">	
										<input type="text" class="inputText" id="menu-advancedPageMenu-shadow-size" name="variables[menu.advancedPageMenu.shadow.size]" value="{$variables['menu.advancedPageMenu.shadow.size']}" />
										<select name="variables[menu.advancedPageMenu.shadow.size.unit]">
											{htmlOptions values=$units output=$units selected=$variables['menu.advancedPageMenu.shadow.size.unit']}
										</select>						
									</div>
								</div>
								
								<div class="formElement">
									<div class="formFieldLabel">
										<label for="menu-advancedPageMenu-shadow-offset">{lang}wcf.acp.style.editor.shadow.offset{/lang}</label>
									</div>
									<div class="formField">	
										<input type="text" class="inputText" id="menu-advancedPageMenu-shadow-offset" name="variables[menu.advancedPageMenu.shadow.offset]" value="{$variables['menu.advancedPageMenu.shadow.offset']}" />
										<select name="variables[menu.advancedPageMenu.shadow.offset.unit]">
											{htmlOptions values=$units output=$units selected=$variables['menu.advancedPageMenu.shadow.offset.unit']}
										</select>						
									</div>
								</div>															
                                                        </fieldset>
                                                </div>
                                </div>
				<div class="formElement colorPicker">
					<div class="formFieldLabel">
						<label for="menu-advancedPageMenu-shadow-color">{lang}wcf.acp.style.editor.color{/lang}</label>
					</div>
					<div class="formField">	
						<input type="text" class="inputText" id="menu-advancedPageMenu-shadow-color" name="variables[menu.advancedPageMenu.shadow.color]" value="{$variables['menu.advancedPageMenu.shadow.color']}" />
						<script type="text/javascript">
							//<![CDATA[
							colorChooser.init('menu-advancedPageMenu-shadow-color');
							//]]>
						</script>
					</div>
				</div>
			</fieldset>					
						
			
		</div>
	</div>