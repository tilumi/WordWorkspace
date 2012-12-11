@serverRedo = (commandsJson,docToProcess) ->
	doc = document.open("text/html","replace")
	doc.write(docToProcess)
	doc.close()

	# commands = jQuery.parseJSON(commandsJson)

	




