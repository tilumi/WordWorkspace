class User < ActiveRecord::Base
  # Include default devise modules. Others available are:
  # :token_authenticatable, :confirmable,
  # :lockable, :timeoutable and :omniauthable
  devise :database_authenticatable, :registerable, :omniauthable,
         :recoverable, :rememberable, :trackable, :validatable

  # Setup accessible (or protected) attributes for your model
  attr_accessible :email, :password, :password_confirmation, :remember_me, :facebook
  attr_accessible :first_name, :last_name, :image
  attr_accessible :provider, :uid

  has_many :authorizations
  has_many :markups
  has_many :outlines

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

  def self.find_for_google_oauth2(access_token, signed_in_resource=nil)
    data = access_token.info
    #if AllowedUser.find_by_email(data["email"].strip)
      # logger.info "XD"
        user = User.where(:email => data["email"]).first
      if user
        created_hash = {:email => data[:email], :first_name => data[:first_name], 
                      :last_name => data[:last_name], :image => data[:image] }
        user.update_attributes(created_hash)
      end
      # logger.info user
      unless user
          user = User.create(first_name: data["first_name"],
                last_name: data["last_name"],
               email: data["email"],
               image: data["image"],
               password: Devise.friendly_token[0,20]
              )
      end
    #end
    user
  end

  def self.find_for_facebook_oauth(auth, signed_in_resource=nil)
    #if AllowedUser.find_by_facebook(auth.info.email)
      user = User.where(:provider => auth.provider, :uid => auth.uid).first
      user ||= User.find_by_facebook(facebook: auth.info.email).first
      logger.info auth.inspect
      if user
        created_hash = {first_name:auth.extra.raw_info.first_name,
                            last_name:auth.extra.raw_info.last_name,
                             provider:auth.provider,
                             uid:auth.uid,
                             facebook:auth.info.email,
                             image: auth.info.image }
        user.update_attributes(created_hash)
      end
      unless user
        user = User.create(first_name:auth.extra.raw_info.first_name,
                            last_name:auth.extra.raw_info.last_name,
                             provider:auth.provider,
                             uid:auth.uid,
                             facebook:auth.info.email,
                             password:Devise.friendly_token[0,20],
                             image: auth.extra.raw_info.image
                             )
        logger.info user.errors.messages
      end
    #end
  user
end

end
