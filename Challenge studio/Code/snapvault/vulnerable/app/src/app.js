// SnapVault frontend helper.
// TODO(dev): move this to a backend call before launch.
const SNAPVAULT_STORAGE = {
  endpoint: window.location.hostname + ":9000",
  region: "us-east-1",
  // read-only key so the gallery can preview objects directly from storage
  accessKey: "SNAPVAULTVIEWERRO",
  secretKey: "viewer-readonly-secret-key-01"
};
// (nothing else wired up yet)
