require 'spec_helper'

describe "allowed_users/new" do
  before(:each) do
    assign(:allowed_user, stub_model(AllowedUser,
      :email => "MyString"
    ).as_new_record)
  end

  it "renders new allowed_user form" do
    render

    # Run the generator again with the --webrat flag if you want to use webrat matchers
    assert_select "form", :action => allowed_users_path, :method => "post" do
      assert_select "input#allowed_user_email", :name => "allowed_user[email]"
    end
  end
end
