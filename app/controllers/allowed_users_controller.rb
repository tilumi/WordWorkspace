class AllowedUsersController < ApplicationController
  # GET /allowed_users
  # GET /allowed_users.json
  def index
    @allowed_users = AllowedUser.all

    respond_to do |format|
      format.html # index.html.erb
      format.json { render json: @allowed_users }
    end
  end

  # GET /allowed_users/1
  # GET /allowed_users/1.json
  def show
    @allowed_user = AllowedUser.find(params[:id])

    respond_to do |format|
      format.html # show.html.erb
      format.json { render json: @allowed_user }
    end
  end

  # GET /allowed_users/new
  # GET /allowed_users/new.json
  def new
    @allowed_user = AllowedUser.new

    respond_to do |format|
      format.html # new.html.erb
      format.json { render json: @allowed_user }
    end
  end

  # GET /allowed_users/1/edit
  def edit
    @allowed_user = AllowedUser.find(params[:id])
  end

  # POST /allowed_users
  # POST /allowed_users.json
  def create
    @allowed_user = AllowedUser.new(params[:allowed_user])

    respond_to do |format|
      if @allowed_user.save
        format.html { redirect_to @allowed_user, notice: 'Allowed user was successfully created.' }
        format.json { render json: @allowed_user, status: :created, location: @allowed_user }
      else
        format.html { render action: "new" }
        format.json { render json: @allowed_user.errors, status: :unprocessable_entity }
      end
    end
  end

  # PUT /allowed_users/1
  # PUT /allowed_users/1.json
  def update
    @allowed_user = AllowedUser.find(params[:id])

    respond_to do |format|
      if @allowed_user.update_attributes(params[:allowed_user])
        format.html { redirect_to @allowed_user, notice: 'Allowed user was successfully updated.' }
        format.json { head :no_content }
      else
        format.html { render action: "edit" }
        format.json { render json: @allowed_user.errors, status: :unprocessable_entity }
      end
    end
  end

  # DELETE /allowed_users/1
  # DELETE /allowed_users/1.json
  def destroy
    @allowed_user = AllowedUser.find(params[:id])
    @allowed_user.destroy

    respond_to do |format|
      format.html { redirect_to allowed_users_url }
      format.json { head :no_content }
    end
  end
end
