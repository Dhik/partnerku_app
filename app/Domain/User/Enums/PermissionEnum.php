<?php

namespace App\Domain\User\Enums;

enum PermissionEnum
{
    // User
    const CreateUser = 'create user';
    const UpdateUser = 'update user';
    const ViewUser = 'view user';
    const DeleteUser = 'delete user';

    // Payroll
    const CreatePayroll = 'create payroll';
    const UpdatePayroll = 'update payroll';
    const ViewPayroll = 'view payroll';
    const DeletePayroll = 'delete payroll';

    // Income
    const CreateIncome = 'create income';
    const UpdateIncome = 'update income';
    const ViewIncome = 'view income';
    const DeleteIncome = 'delete income';

    // Other Spent
    const CreateOtherSpent = 'create other spent';
    const UpdateOtherSpent = 'update other spent';
    const ViewOtherSpent = 'view other spent';
    const DeleteOtherSpent = 'delete other spent';

    // Profile
    const ViewProfile = 'view profile';
    const ChangeOwnPassword = 'change own password';

    // Tenant
    const ViewTenant = 'view tenant';
    const CreateTenant = 'create tenant';
    const UpdateTenant = 'update tenant';
    const DeleteTenant = 'delete tenant';
    const AssignTenantUser = 'assign tenant user';

    // Result
    const ViewResult = 'view result';
    const CreateResult = 'create result';
    const UpdateResult = 'update result';
    const DeleteResult = 'delete result';

    // KOL
    const ViewKOL = 'view kol';
    const CreateKOL = 'create kol';
    const UpdateKOL = 'update kol';
    const DeleteKOL = 'delete kol';

    // Campaign Content
    const ViewCampaignContent = 'view campaign content';
    const CreateCampaignContent = 'create campaign content';
    const UpdateCampaignContent = 'update campaign content';
    const DeleteCampaignContent = 'delete campaign content';

    // Campaign 
    const ViewCampaign = 'view campaign';
    const CreateCampaign = 'create campaign';
    const UpdateCampaign = 'update campaign';
    const DeleteCampaign = 'delete campaign';
}
