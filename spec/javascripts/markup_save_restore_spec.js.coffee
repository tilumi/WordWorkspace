describe "Markup can save", ->
	it "Markup can restore from save", ->
		loadFixtures "complex_html.html"
		rangy.init()
