require 'spec_helper'

describe "allowed_users/index" do
  before(:each) do
    assign(:allowed_users, [
      stub_model(AllowedUser,
        :email => "Email"
      ),
      stub_model(AllowedUser,
        :email => "Email"
      )
    ])
  end

  it "renders a list of allowed_users" do
    render
    # Run the generator again with the --webrat flag if you want to use webrat matchers
    assert_select "tr>td", :text => "Email".to_s, :count => 2
  end
end
