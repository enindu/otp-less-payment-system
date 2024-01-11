<?php

use App\Controllers\Admin\Accounts as AdminAccounts;
use App\Controllers\Admin\Admins as AdminAdmins;
use App\Controllers\Admin\Base as AdminBase;
use App\Controllers\Admin\Categories as AdminCategories;
use App\Controllers\Admin\Images as AdminImages;
use App\Controllers\Admin\Messages as AdminMessages;
use App\Controllers\Admin\Reports as AdminReports;
use App\Controllers\Admin\Users as AdminUsers;
use App\Controllers\User\About as UserAbout;
use App\Controllers\User\Accounts as UserAccounts;
use App\Controllers\User\Actions as UserActions;
use App\Controllers\User\Base as UserBase;
use App\Controllers\User\Contact as UserContact;
use App\Middleware\AdminAuth;
use App\Middleware\UserAuth;
use Slim\Routing\RouteCollectorProxy;

$app->group("/admin", function(RouteCollectorProxy $admin) {
  $admin->get("", AdminBase::class . ":base");
  $admin->group("/accounts", function(RouteCollectorProxy $accounts) {
    $accounts->map(["GET", "POST"], "/login", AdminAccounts::class . ":login");
    // $accounts->map(["GET", "POST"], "/register", AdminAccounts::class . ":register");
    $accounts->map(["GET", "POST"], "/logout", AdminAccounts::class . ":logout");
    $accounts->post("/change-information", AdminAccounts::class . ":changeInformation");
    $accounts->post("/change-password", AdminAccounts::class . ":changePassword");
    $accounts->get("/profile", AdminAccounts::class . ":profile");
  });
  // $admin->group("/roles", function(RouteCollectorProxy $roles) {
  //   $roles->get("", AdminRoles::class . ":base");
  //   $roles->get("/all", AdminRoles::class . ":all");
  //   $roles->post("/add", AdminRoles::class . ":add");
  //   $roles->post("/remove", AdminRoles::class . ":remove");
  // });
  // $admin->group("/sections", function(RouteCollectorProxy $sections) {
  //   $sections->get("", AdminSections::class . ":base");
  //   $sections->get("/all", AdminSections::class . ":all");
  //   $sections->post("/add", AdminSections::class . ":add");
  //   $sections->post("/remove", AdminSections::class . ":remove");
  // });
  $admin->group("/admins", function(RouteCollectorProxy $admins) {
    $admins->get("", AdminAdmins::class . ":base");
    $admins->get("/all", AdminAdmins::class . ":all");
    $admins->post("/add", AdminAdmins::class . ":add");
    $admins->post("/activate", AdminAdmins::class . ":activate");
    $admins->post("/deactivate", AdminAdmins::class . ":deactivate");
    $admins->post("/remove", AdminAdmins::class . ":remove");
  });
  $admin->group("/users", function(RouteCollectorProxy $users) {
    $users->get("", AdminUsers::class . ":base");
    $users->get("/all", AdminUsers::class . ":all");
    $users->post("/add", AdminUsers::class . ":add");
    $users->post("/activate", AdminUsers::class . ":activate");
    $users->post("/deactivate", AdminUsers::class . ":deactivate");
    $users->post("/remove", AdminUsers::class . ":remove");
  });
  // $admin->group("/contents", function(RouteCollectorProxy $contents) {
  //   $contents->get("", AdminContents::class . ":base");
  //   $contents->get("/all", AdminContents::class . ":all");
  //   $contents->post("/add", AdminContents::class . ":add");
  //   $contents->post("/update", AdminContents::class . ":update");
  //   $contents->post("/remove", AdminContents::class . ":remove");
  //   $contents->get("/{id}", AdminContents::class . ":single");
  // });
  $admin->group("/images", function(RouteCollectorProxy $images) {
    $images->get("", AdminImages::class . ":base");
    $images->get("/all", AdminImages::class . ":all");
    $images->post("/add", AdminImages::class . ":add");
    $images->post("/remove", AdminImages::class . ":remove");
  });
  // $admin->group("/files", function(RouteCollectorProxy $files) {
  //   $files->get("", AdminFiles::class . ":base");
  //   $files->get("/all", AdminFiles::class . ":all");
  //   $files->post("/add", AdminFiles::class . ":add");
  //   $files->post("/remove", AdminFiles::class . ":remove");
  // });
  $admin->group("/categories", function(RouteCollectorProxy $categories) {
    $categories->get("", AdminCategories::class . ":base");
    $categories->get("/all", AdminCategories::class . ":all");
    $categories->post("/add", AdminCategories::class . ":add");
    $categories->post("/update", AdminCategories::class . ":update");
    $categories->post("/remove", AdminCategories::class . ":remove");
    $categories->get("/{id}", AdminCategories::class . ":single");
  });
  // $admin->group("/subcategories", function(RouteCollectorProxy $subcategories) {
  //   $subcategories->get("", AdminSubcategories::class . ":base");
  //   $subcategories->get("/all", AdminSubcategories::class . ":all");
  //   $subcategories->post("/add", AdminSubcategories::class . ":add");
  //   $subcategories->post("/update", AdminSubcategories::class . ":update");
  //   $subcategories->post("/remove", AdminSubcategories::class . ":remove");
  //   $subcategories->get("/{id}", AdminSubcategories::class . ":single");
  // });
  $admin->group("/messages", function(RouteCollectorProxy $messages) {
    $messages->get("", AdminMessages::class . ":base");
    $messages->get("/all", AdminMessages::class . ":all");
    $messages->post("/remove", AdminMessages::class . ":remove");
    $messages->get("/{id}", AdminMessages::class . ":single");
  });
  $admin->group("/reports", function(RouteCollectorProxy $reports) {
    $reports->get("", AdminReports::class . ":base");
  });
})->add(new AdminAuth($container));

$app->group("", function(RouteCollectorProxy $user) use ($container) {
  $user->get("/", UserBase::class . ":base");
  $user->group("/accounts", function(RouteCollectorProxy $accounts) {
    $accounts->map(["GET", "POST"], "/login", UserAccounts::class . ":login");
    $accounts->map(["GET", "POST"], "/register", UserAccounts::class . ":register");
    $accounts->get("/logout", UserAccounts::class . ":logout");
    $accounts->post("/change-information", UserAccounts::class . ":changeInformation");
    $accounts->post("/change-password", UserAccounts::class . ":changePassword");
    $accounts->get("/profile", UserAccounts::class . ":profile");
    $accounts->get("/settings", UserAccounts::class . ":settings");
  })->add(new UserAuth($container));
  $user->group("/actions", function(RouteCollectorProxy $actions) {
    $actions->map(["GET", "POST"], "/transfer-money", UserActions::class . ":transferMoney");
    $actions->map(["GET", "POST"], "/pay-online", UserActions::class . ":payOnline");
    $actions->map(["GET", "POST"], "/recharge-phone", UserActions::class . ":rechargePhone");
    $actions->get("/exchange-rates", UserActions::class . ":exchangeRates");
    $actions->get("/transactions", UserActions::class . ":transactions");
  })->add(new UserAuth($container));
  $user->group("/about", function(RouteCollectorProxy $about) {
    $about->get("", UserAbout::class . ":base");
  });
  $user->group("/contact", function(RouteCollectorProxy $contact) {
    $contact->map(["GET", "POST"], "", UserContact::class . ":base");
  });
});
