require 'spec_helper'

describe "allowed_users/show" do
  before(:each) do
    @allowed_user = assign(:allowed_user, stub_model(AllowedUser,
      :email => "Email"
    ))
  end

  it "renders attributes in <p>" do
    render
    # Run the generator again with the --webrat flag if you want to use webrat matchers
    rendered.should match(/Email/)
  end
end
