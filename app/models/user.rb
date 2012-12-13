class User < ActiveRecord::Base
  # Include default devise modules. Others available are:
  # :token_authenticatable, :confirmable,
  # :lockable, :timeoutable and :omniauthable
  devise :database_authenticatable, :registerable,
         :recoverable, :rememberable, :trackable, :validatable, :confirmable

  # Setup accessible (or protected) attributes for your model
  attr_accessible :email, :password, :password_confirmation, :remember_me
  attr_accessible :first_name, :last_name, :image

  has_many :authorizations
  has_many :markups

  def self.create_from_auth!(hash)
    created_hash = {:email => hash[:info][:email], :first_name => hash[:info][:first_name], 
                    :last_name => hash[:info][:last_name], :image => hash[:info][:image] }
    user = (created_hash[:email].nil? ? nil : User.find_by_email(created_hash[:email])) || User.new(created_hash)
    # logger.info created_hash.inspect
    if user.email
      user.confirm!
      user.update_attributes(created_hash)
    elsif user
      user.save!
    end
    user
  end

end
