require 'spec_helper'

describe "videos/index" do
  before(:each) do
    assign(:videos, [
      stub_model(Video,
        :document_id => 1,
        :path => "Path"
      ),
      stub_model(Video,
        :document_id => 1,
        :path => "Path"
      )
    ])
  end

  it "renders a list of videos" do
    render
    # Run the generator again with the --webrat flag if you want to use webrat matchers
    assert_select "tr>td", :text => 1.to_s, :count => 2
    assert_select "tr>td", :text => "Path".to_s, :count => 2
  end
end
