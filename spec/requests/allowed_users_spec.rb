require 'spec_helper'

describe "AllowedUsers" do
  describe "GET /allowed_users" do
    it "works! (now write some real specs)" do
      # Run the generator again with the --webrat flag if you want to use webrat methods/matchers
      get allowed_users_path
      response.status.should be(200)
    end
  end
end
