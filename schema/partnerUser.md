```js
input CreatePartnerUserInput {
  name: String! @rules(apply: ["required"])
  email: String! @rules(apply: ["required", "email", "unique:partner_users,email"])
  password: String @bcrypt
  provider: String
  provider_id: String
  partner_id: ID
  phone: String
  position: String
  avatar: String
}

input UpdatePartnerUserInput {
  id: ID!
  name: String
  email: String
  partner_id: ID
  phone: String
  position: String
  avatar: String
  phone_verified_at: DateTime
}

input PartnerUserPhoneVerificationInput {
  phone: String! @rules(apply: ["required"])
}

input PartnerUserLoginInput {
  email: String! @rules(apply: ["required", "email"])
  password: String! @rules(apply: ["required"])
}

input PartnerUserSocialLoginInput {
  provider: String! @rules(apply: ["required", "in:facebook,google"])
  token: String! @rules(apply: ["required"])
}

type PartnerUserAuthPayload {
  access_token: String
  user: PartnerUser
}

type PartnerUser {
  id: ID
  name: String
  email: String
  phone: String
  avatar: String
  position: String
  phone_verified_at: DateTime
  partner: Partner @belongsTo
}

type mutation {
  createPartnerUser(input: CreatePartnerUserInput): PartnerUser

  updatePartnerUser(input: UpdatePartnerUserInput): PartnerUser

  partnerUserLogin(input: PartnerUserLoginInput): PartnerUserAuthPayload

  partnerUserSocialLogin(input: PartnerUserSocialLoginInput): PartnerUserAuthPayload

  partnerUserPhoneVerification(input: PartnerUserPhoneVerificationInput): String
}
```
- **createPartnerUser:** Create new user (Sign up).
- **updatePartnerUser:** Edit an existing user (each field could be updated independently).
- **partnerUserLogin:** Log in using the traditional sign in form.
- **partnerUserSocialLogin:** Log in using social network.
- **partnerUserPhoneVerification:** Verify user phone number through SMS verification code.