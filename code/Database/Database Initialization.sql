
DROP TABLE Accommodation;
DROP TABLE Service;
DROP TABLE Demographic;
DROP TABLE Organization;

CREATE TABLE Organization(
  orgID INTEGER PRIMARY KEY,

  description VARCHAR(512),
  layerID INTEGER,
  geoLocation CHAR(30)
);

CREATE TABLE Accommodation(
  orgID INTEGER PRIMARY KEY,

  accommodatesDisabilities BOOLEAN, #join of both some units and buildings
  qualifiesForRentalAssitance BOOLEAN,
  canYouthBeKeyTenant BOOLEAN,
  canSmokeInBulding BOOLEAN,
  canSmokeInUnit BOOLEAN,
  isFullyFurnished BOOLEAN,
  isKelownaResident BOOLEAN,
  isPetFriendly BOOLEAN,
  isSelfContained BOOLEAN,
  hasMaxDurationOfStay BOOLEAN,
  hasWaitList BOOLEAN,
  hasRTA BOOLEAN,
  onlyServesAboriginal BOOLEAN,
  unitsAreRGI BOOLEAN,
  unitsAreLowerEndOfMarket BOOLEAN,




  FOREIGN KEY (orgID) REFERENCES Organization(orgID)
    ON UPDATE CASCADE
    ON DELETE NO ACTION

);

CREATE TABLE Service(
  orgID INTEGER PRIMARY KEY,

  caseManagement BOOLEAN,
  clothingOrHouseHoldGoods BOOLEAN,
  harmReduction BOOLEAN,
  healthOrDental BOOLEAN,
  hygiene BOOLEAN,
  laundry BOOLEAN,
  meals BOOLEAN,
  parking BOOLEAN,
  referrals BOOLEAN,
  showers BOOLEAN,
  storage BOOLEAN,

  FOREIGN KEY (orgID) REFERENCES Organization(orgID)
    ON UPDATE CASCADE
    ON DELETE NO ACTION
);


CREATE TABLE Demographic(
  orgID INTEGER PRIMARY KEY,

  age INTEGER, #age means >= age, other = -1, 0 = unspecified
  mustAbstainFromDrugsAndAlcohol BOOLEAN,
  male BOOLEAN,
  female BOOLEAN,
  hasMinimumIncomeRequirement BOOLEAN,
  hasMaximumIncomeRequirement BOOLEAN,
  requiresCleanTime BOOLEAN,
  requiresIncomeOrDisabilityAssitance BOOLEAN,
  requiresTreatmentFirst BOOLEAN,
  professionalSupportRequired BOOLEAN,
  targetsCouples BOOLEAN,
  targetsFamilies BOOLEAN,
  targetsIndividuals BOOLEAN,
  transgender BOOLEAN,

  FOREIGN KEY (orgID) REFERENCES Organization(orgID)
    ON UPDATE CASCADE
    ON DELETE NO ACTION
);
