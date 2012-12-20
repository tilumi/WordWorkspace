require "spec_helper"

describe AllowedUsersController do
  describe "routing" do

    it "routes to #index" do
      get("/allowed_users").should route_to("allowed_users#index")
    end

    it "routes to #new" do
      get("/allowed_users/new").should route_to("allowed_users#new")
    end

    it "routes to #show" do
      get("/allowed_users/1").should route_to("allowed_users#show", :id => "1")
    end

    it "routes to #edit" do
      get("/allowed_users/1/edit").should route_to("allowed_users#edit", :id => "1")
    end

    it "routes to #create" do
      post("/allowed_users").should route_to("allowed_users#create")
    end

    it "routes to #update" do
      put("/allowed_users/1").should route_to("allowed_users#update", :id => "1")
    end

    it "routes to #destroy" do
      delete("/allowed_users/1").should route_to("allowed_users#destroy", :id => "1")
    end

  end
end
