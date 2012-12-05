describe  "Sample", ->
  it "1 is to be 1", ->
    expect(1).toEqual(1)

  it "Load Fixture", ->
    loadFixtures("sample_fixture.html")
    expect($("#sample")).toHaveText("Sample")

