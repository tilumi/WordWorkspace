require 'spec_helper'

describe "allowed_users/edit" do
  before(:each) do
    @allowed_user = assign(:allowed_user, stub_model(AllowedUser,
      :email => "MyString"
    ))
  end

  it "renders the edit allowed_user form" do
    render

    # Run the generator again with the --webrat flag if you want to use webrat matchers
    assert_select "form", :action => allowed_users_path(@allowed_user), :method => "post" do
      assert_select "input#allowed_user_email", :name => "allowed_user[email]"
    end
  end
end
