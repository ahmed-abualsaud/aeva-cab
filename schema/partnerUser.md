## Partner User Schema

```js
input CreateUserInput {
  name: String! @rules(apply: ["required"])
  email: String! @rules(apply: ["required", "email", "unique:users,email"])
  phone: String @rules(apply: ["unique:users,phone"])
  password: String @bcrypt
  provider: String
  provider_id: String
  partner_id: ID
  position: String
  avatar: String
}

input UpdateUserInput {
  id: ID! @rules(apply: ["required"])
  name: String
  email: String
  partner_id: ID
  phone: String
  position: String
  avatar: String
  phone_verified_at: DateTime
}

input GetPartnerTripUsersInput {
  partner_id: ID! @rules(apply: ["required"])
  trip_id: ID! @rules(apply: ["required"])
  status: String! @rules(apply: ["required", "in:subscribed,notSubscribed,notVerified"])
}

input UserPhoneVerificationInput {
  phone: String! @rules(apply: ["required"])
}

input UserLoginInput {
  email: String! @rules(apply: ["required", "email"])
  password: String! @rules(apply: ["required"])
}

input UserSocialLoginInput {
  provider: String! @rules(apply: ["required", "in:facebook,google"])
  token: String! @rules(apply: ["required"])
}

input ConfirmPartnerTripUserInput {
  user_id: ID! @rules(apply: ["required"])
  subscription_code: String! @rules(apply: ["required"])
}

type UserAuthPayload {
  access_token: String
  user: User
}

type User {
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
  createUser(input: CreateUserInput): User
  updateUser(input: UpdateUserInput): User
  userLogin(input: UserLoginInput): UserAuthPayload
  userSocialLogin(input: UserSocialLoginInput): UserAuthPayload
  userPhoneVerification(input: UserPhoneVerificationInput): String
  confirmPartnerTripUser(input: confirmPartnerTripUserInput): PartnerTrip
}

type query {
  partnerTripUsers(input: GetPartnerTripUsersInput): [User]
  partnerTripStations(partner_trip_id: ID): [PartnerTripStation]
}
```

### Mutations

- **createUser:** Create new user (Sign up).
- **updateUser:** Edit an existing user (each field could be updated independently).
- **userLogin:** Log in using the traditional sign in form.
- **userSocialLogin:** Log in using social network.
- **userPhoneVerification:** Verify user phone number through SMS verification code.
- **confirmPartnerTripUser:** Subscribe for a trip using the previously sent subscription code.

### Queries

- **partnerTripUsers:** Return collection of all users subscribed, not subscribed, or not verified for a specific trip.
- **partnerTripStations:** Return collection of all stations associated with a specific trip.